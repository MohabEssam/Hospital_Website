<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use App\Services\DoctorScheduleService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = trim((string) $request->search);

        $doctors = Doctor::query()
            ->with('department')
            ->withCount([
                'patients',
                'appointments',
                'appointments as today_appointments_count' => fn (Builder $query) => $query->whereDate('appointment_date', today()),
            ])
            ->when($search, function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('doctor_code', 'like', "%{$search}%")
                        ->orWhere('specialty', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('department', function (Builder $query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('department_id'), fn (Builder $query) => $query->where('department_id', $request->integer('department_id')))
            ->when($request->filled('availability_status'), fn (Builder $query) => $query->where('availability_status', $request->string('availability_status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('doctors._table', ['doctors' => $doctors])->render(),
                'url' => $request->fullUrl(),
            ]);
        }

        return view('doctors.index', [
            'doctors' => $doctors,
            'departments' => Department::query()->orderBy('name')->get(),
            'filters' => [
                'search' => $search,
                'department_id' => $request->input('department_id'),
                'availability_status' => $request->input('availability_status'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('doctors.create', [
            'doctor' => new Doctor(),
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')
                ->storeAs('doctors', time() . '_' . $request->file('avatar')->getClientOriginalName(), 'public');
        }

        $password = 'Dr@'.strtoupper(Str::random(3)).rand(100, 999);

        DB::transaction(function () use ($data, $request, $password): void {
            $user = User::create([
                'name' => $data['name'],
                'email' => $request->email,
                'password' => bcrypt($password),
                'role' => User::ROLE_DOCTOR,
            ]);

            Doctor::create([
                ...$data,
                'user_id' => $user->getKey(),
            ]);
        });

        return redirect()
            ->route('doctors.index')
            ->with(
                'success',
                "✅ Doctor account created successfully!\n".
                "📧 Email: ".$request->email."\n".
                "🔑 Password: ".$password."\n".
                "⚠️ Please share these credentials with the doctor securely."
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor): View
    {
        $doctor->load('department');
        $doctor->loadCount(['patients', 'appointments']);

        $recentAppointments = Appointment::query()
            ->whereBelongsTo($doctor)
            ->with('patient')
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->take(4)
            ->get();

        $chartDays = collect(range(0, 5))
            ->map(fn (int $offset) => today()->startOfWeek()->addDays($offset))
            ->values();

        $newPatientSeries = $chartDays
            ->map(fn (Carbon $date) => Appointment::query()
                ->whereBelongsTo($doctor)
                ->whereDate('appointment_date', $date)
                ->whereHas('patient', fn (Builder $query) => $query->where('status', Patient::STATUS_NEW))
                ->count())
            ->all();

        $followUpSeries = $chartDays
            ->map(fn (Carbon $date) => Appointment::query()
                ->whereBelongsTo($doctor)
                ->whereDate('appointment_date', $date)
                ->whereHas('patient', fn (Builder $query) => $query->where('status', '!=', Patient::STATUS_NEW))
                ->count())
            ->all();

        $todaySchedule = Appointment::query()
            ->whereBelongsTo($doctor)
            ->with('patient')
            ->whereDate('appointment_date', today())
            ->orderBy('start_time')
            ->get();

        return view('doctors.show', [
            'doctor' => $doctor,
            'recentAppointments' => $recentAppointments,
            'feedbackPatients' => $recentAppointments,
            'chartLabels' => $chartDays->map->format('l')->all(),
            'newPatientSeries' => $newPatientSeries,
            'followUpSeries' => $followUpSeries,
            'todaySchedule' => $todaySchedule,
        ]);
    }

    public function schedule(Request $request, Doctor $doctor): View
    {
        abort_unless($request->user()->isAdmin() || $request->user()->doctorProfile?->is($doctor), 403);

        app(DoctorScheduleService::class)->seedDefaultSchedule($doctor);
        $doctor->load('schedules');

        $month = max(1, min(12, $request->integer('month', today()->month)));
        $year = max(2020, $request->integer('year', today()->year));
        $calendarDate = Carbon::create($year, $month, 1);

        $appointments = Appointment::query()
            ->whereBelongsTo($doctor)
            ->with('patient')
            ->whereBetween('appointment_date', [
                $calendarDate->copy()->startOfMonth()->toDateString(),
                $calendarDate->copy()->endOfMonth()->toDateString(),
            ])
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Appointment $appointment) => $appointment->appointment_date->toDateString());

        return view('doctors.schedule', [
            'doctor' => $doctor,
            'calendarDate' => $calendarDate,
            'appointmentsByDate' => $appointments,
            'allDoctors' => Doctor::query()->orderBy('name')->get(['id', 'name']),
            'weeklySchedules' => $doctor->schedules->groupBy('day_of_week'),
        ]);
    }

    public function updateWeeklySchedule(Request $request, Doctor $doctor): RedirectResponse
    {
        abort_unless($request->user()->isAdmin() || $request->user()->doctorProfile?->is($doctor), 403);

        $validated = $request->validate([
            'slots' => ['array'],
            'slots.*' => ['array'],
            'slots.*.*.start_time' => ['nullable', 'date_format:H:i'],
            'slots.*.*.end_time' => ['nullable', 'date_format:H:i'],
            'slots.*.*.is_available' => ['nullable', 'boolean'],
        ]);

        $doctor->schedules()->delete();

        foreach (($validated['slots'] ?? []) as $dayOfWeek => $slots) {
            if ((int) $dayOfWeek < 0 || (int) $dayOfWeek > 6) {
                continue;
            }

            foreach ($slots as $slot) {
                if (blank($slot['start_time'] ?? null) || blank($slot['end_time'] ?? null)) {
                    continue;
                }

                if (Carbon::createFromFormat('H:i', $slot['end_time'])->lessThanOrEqualTo(Carbon::createFromFormat('H:i', $slot['start_time']))) {
                    continue;
                }

                DoctorSchedule::create([
                    'doctor_id' => $doctor->getKey(),
                    'day_of_week' => (int) $dayOfWeek,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_available' => (bool) ($slot['is_available'] ?? false),
                ]);
            }
        }

        return redirect()
            ->route('doctors.schedule', $doctor)
            ->with('status', 'Weekly schedule updated successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor): View
    {
        return view('doctors.edit', [
            'doctor' => $doctor,
            'departments' => Department::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($doctor->avatar && Storage::disk('public')->exists($doctor->avatar)) {
                Storage::disk('public')->delete($doctor->avatar);
            }

            $data['avatar'] = $request->file('avatar')
                ->storeAs('doctors', time() . '_' . $request->file('avatar')->getClientOriginalName(), 'public');
        }

        $doctor->update($data);

        return redirect()
            ->route('doctors.show', $doctor)
            ->with('status', 'Doctor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->avatar && Storage::disk('public')->exists($doctor->avatar)) {
            Storage::disk('public')->delete($doctor->avatar);
        }

        $doctor->delete();

        return redirect()
            ->route('doctors.index')
            ->with('status', 'Doctor deleted successfully.');
    }
}

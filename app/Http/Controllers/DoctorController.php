<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $doctors = Doctor::query()
            ->with('department')
            ->withCount([
                'patients',
                'appointments',
                'appointments as today_appointments_count' => fn (Builder $query) => $query->whereDate('appointment_date', today()),
            ])
            ->when(
                $request->filled('search'),
                fn (Builder $query) => $query->where(function (Builder $nested) use ($request): void {
                    $term = (string) $request->string('search');

                    $nested
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('doctor_code', 'like', "%{$term}%")
                        ->orWhere('specialty', 'like', "%{$term}%")
                        ->orWhereHas('department', fn (Builder $departmentQuery) => $departmentQuery->where('name', 'like', "%{$term}%"));
                }),
            )
            ->when($request->filled('department_id'), fn (Builder $query) => $query->where('department_id', $request->integer('department_id')))
            ->when($request->filled('specialty'), fn (Builder $query) => $query->where('specialty', $request->string('specialty')))
            ->when($request->filled('availability_status'), fn (Builder $query) => $query->where('availability_status', $request->string('availability_status')))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('doctors.index', [
            'doctors' => $doctors,
            'departments' => Department::query()->orderBy('name')->get(),
            'specialties' => Doctor::query()
                ->whereNotNull('specialty')
                ->select('specialty')
                ->distinct()
                ->orderBy('specialty')
                ->pluck('specialty'),
            'filters' => $request->only(['search', 'department_id', 'specialty', 'availability_status']),
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
        $doctor = Doctor::create($request->validated());

        return redirect()
            ->route('doctors.show', $doctor)
            ->with('status', 'Doctor created successfully.');
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
        ]);
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
        $doctor->update($request->validated());

        return redirect()
            ->route('doctors.show', $doctor)
            ->with('status', 'Doctor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor): RedirectResponse
    {
        $doctor->delete();

        return redirect()
            ->route('doctors.index')
            ->with('status', 'Doctor deleted successfully.');
    }
}

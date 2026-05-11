<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Notifications\AppointmentBookedNotification;
use App\Services\AppointmentConflictService;
use App\Services\DoctorScheduleService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Validator;

class BookingController extends Controller
{
    public function __construct(
        private readonly DoctorScheduleService $scheduleService,
        private readonly AppointmentConflictService $conflictService,
    ) {}

    public function myBookings(Request $request): View
    {
        $statusFilter = $request->input('status', 'all');
        $phone = $request->input('phone', session('phone'));
        $searched = false;

        // Build query based on authentication status
        if ($request->user()) {
            // Authenticated user - fetch by patient_id
            $patientProfile = $request->user()->patientProfile;
            $query = $patientProfile
                ? Appointment::query()->where('patient_id', $patientProfile->getKey())
                : Appointment::whereRaw('1 = 0');
        } else {
            // Guest user - fetch by phone number
            if ($request->filled('phone')) {
                $request->validate([
                    'phone' => ['required', 'string', 'min:7', 'max:20'],
                ]);
                session(['phone' => $request->input('phone')]);
                $phone = $request->input('phone');
                $searched = true;
            }

            $query = $phone
                ? Appointment::query()->where('phone_number', $phone)
                : Appointment::whereRaw('1 = 0');
        }

        $query
            ->select([
                'id',
                'patient_id',
                'doctor_id',
                'appointment_date',
                'start_time',
                'end_time',
                'status',
                'treatment',
                'notes',
                'phone_number',
                'fee',
            ])
            ->with([
                'doctor:id,name,department_id,specialty',
                'doctor.department:id,name',
            ]);

        // Apply status filter
        if ($statusFilter !== 'all' && in_array($statusFilter, Appointment::statusOptions(), true)) {
            $query->where('status', $statusFilter);
        }

        $appointments = $query
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        return view('website.bookings.index', [
            'appointments' => $appointments,
            'statusFilter' => $statusFilter,
            'phone' => $phone,
            'searched' => $searched,
        ]);
    }

    public function create(Request $request): View
    {
        $doctors = Doctor::query()
            ->select([
                'id',
                'department_id',
                'name',
                'specialty',
                'consultation_fee',
                'availability_status',
                'avatar',
                'years_of_experience',
                'rating',
            ])
            ->with('department:id,name')
            ->where('availability_status', Doctor::STATUS_AVAILABLE)
            ->orderBy('name')
            ->get();

        $doctors->each(fn (Doctor $doctor) => $this->scheduleService->seedDefaultSchedule($doctor));

        $preselectedId = (int) $request->input('doctor_id', 0);
        $preselectedDoctor = $preselectedId ? $doctors->firstWhere('id', $preselectedId) : null;

        $weeklySlots = $preselectedDoctor
            ? $this->scheduleService->weeklySlots($preselectedDoctor)
            : collect();

        return view('website.bookings.create', [
            'doctors' => $doctors,
            'departments' => Department::query()->active()->orderBy('name')->get(['id', 'name', 'is_active']),
            'preselectedDoctor' => $preselectedDoctor,
            'weeklySlots' => $weeklySlots,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'treatment' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'min:7', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $patient = $user->patientProfile;

        abort_unless($patient, 403, 'Only patients can book appointments.');

        $doctor = Doctor::findOrFail($validated['doctor_id']);

        $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = $startTime->copy()->addMinutes(30)->format('H:i');

        $validator = validator($validated);
        $validator->after(function (Validator $validator) use ($doctor, $validated, $startTime, $endTime): void {
            if (! $doctor->isAvailable()) {
                $validator->errors()->add('doctor_id', 'The selected doctor is currently unavailable.');
            }

            if (! $this->scheduleService->slotIsAvailable($doctor, $validated['appointment_date'], $startTime->format('H:i'))) {
                $validator->errors()->add('start_time', 'This time slot is not available in the doctor schedule.');
            }

            if ($this->conflictService->hasConflict(
                $doctor->getKey(),
                $validated['appointment_date'],
                $startTime->format('H:i'),
                $endTime,
            )) {
                $validator->errors()->add('start_time', 'This appointment slot has just been booked. Please choose another time.');
            }
        });
        $validator->validate();

        $appointment = DB::transaction(fn () => Appointment::create([
            'patient_id' => $patient->getKey(),
            'doctor_id' => $doctor->getKey(),
            'department_id' => $doctor->department_id,
            'appointment_date' => $validated['appointment_date'],
            'start_time' => $startTime->format('H:i'),
            'end_time' => $endTime,
            'status' => Appointment::STATUS_PENDING,
            'treatment' => $validated['treatment'],
            'notes' => $validated['notes'] ?? '',
            'phone_number' => $validated['phone_number'],
            'fee' => $doctor->consultation_fee ?? 0,
        ]));

        $appointment->load(['doctor', 'patient.user']);

        if ($appointment->patient?->user) {
            $appointment->patient->user->notify(new AppointmentBookedNotification($appointment));
        }

        if ($doctor->email) {
            Notification::route('mail', $doctor->email)
                ->notify(new AppointmentBookedNotification($appointment));
        }

        return redirect()->route('website.booking.status', $appointment)
            ->with('status', 'Appointment booked successfully!');
    }

    public function show(Request $request, Appointment $appointment): View
    {
        $patient = $request->user()->patientProfile;
        abort_unless($patient && $appointment->patient_id === $patient->getKey(), 403);

        $appointment->load(['doctor.department', 'patient']);

        return view('website.bookings.show', [
            'appointment' => $appointment,
        ]);
    }

    public function slots(Request $request, Doctor $doctor): JsonResponse
    {
        $this->scheduleService->seedDefaultSchedule($doctor);
        $weeklySlots = $this->scheduleService->weeklySlots($doctor);

        $formatted = $weeklySlots->map(fn (array $day) => [
            'date' => $day['date']->toDateString(),
            'date_label' => $day['date']->format('D, M d'),
            'slots' => $day['slots']->values()->all(),
        ])->values();

        return response()->json(['days' => $formatted]);
    }

    public function cancel(Request $request, Appointment $appointment): RedirectResponse
    {
        $patientProfile = $request->user()->patientProfile;

        abort_unless(
            $patientProfile && $appointment->patient_id === $patientProfile->getKey(),
            403,
            'You are not authorized to cancel this appointment.'
        );

        // Only pending appointments can be cancelled
        if ($appointment->status !== Appointment::STATUS_PENDING) {
            return redirect()->route('my-bookings')
                ->with('error', 'Only pending appointments can be cancelled.');
        }

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return redirect()->route('my-bookings')
            ->with('status', 'Appointment cancelled successfully.');
    }
}

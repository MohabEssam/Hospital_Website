<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Notifications\AppointmentBookedNotification;
use App\Services\AppointmentConflictService;
use App\Services\DoctorScheduleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Validator;

class BookingController extends Controller
{
    public function myBookings(Request $request): View
    {
        $user = $request->user();
        $patientProfile = $user->patientProfile;

        $appointments = $patientProfile
            ? Appointment::where('patient_id', $patientProfile->getKey())
                ->with(['doctor.department'])
                ->orderByDesc('appointment_date')
                ->paginate(10)
            : collect();

        return view('website.bookings.index', [
            'appointments' => $appointments,
        ]);
    }

    public function create(): View
    {
        $doctors = Doctor::query()
            ->with('department')
            ->where('availability_status', Doctor::STATUS_AVAILABLE)
            ->orderBy('name')
            ->get();

        $doctors->each(fn (Doctor $doctor) => app(DoctorScheduleService::class)->seedDefaultSchedule($doctor));

        return view('website.bookings.create', [
            'doctors' => $doctors,
            'departments' => Department::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'treatment' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $patient = $user->patientProfile;

        abort_unless($patient, 403, 'Only patients can book appointments.');

        $doctor = Doctor::findOrFail($validated['doctor_id']);

        $startTime = \Carbon\Carbon::createFromFormat('H:i', $validated['start_time']);
        $endTime = $startTime->copy()->addMinutes(30)->format('H:i');

        $validator = validator($validated);
        $validator->after(function (Validator $validator) use ($doctor, $validated, $startTime, $endTime): void {
            if (! $doctor->isAvailable()) {
                $validator->errors()->add('doctor_id', 'The selected doctor is currently unavailable.');
            }

            if (! app(DoctorScheduleService::class)->slotIsAvailable($doctor, $validated['appointment_date'], $startTime->format('H:i'))) {
                $validator->errors()->add('start_time', 'This time slot is not available in the doctor schedule.');
            }

            if (app(AppointmentConflictService::class)->hasConflict(
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

        return redirect()->route('my-bookings')
            ->with('status', 'Appointment booked successfully!');
    }
}

<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookDoctorAppointmentRequest;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Notifications\AppointmentBookedNotification;
use App\Services\DoctorScheduleService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class WebDoctorController extends Controller
{
    public function index(Request $request): View
    {
        $doctors = Doctor::query()
            ->select([
                'id',
                'department_id',
                'name',
                'specialty',
                'biography',
                'phone',
                'email',
                'availability_status',
                'avatar',
                'years_of_experience',
                'rating',
            ])
            ->with('department:id,name')
            ->when(
                $request->filled('department_id'),
                fn (Builder $query) => $query->where('department_id', $request->integer('department_id')),
            )
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('website.doctors.index', [
            'doctors' => $doctors,
            'departments' => Department::query()->active()->orderBy('name')->get(['id', 'name', 'is_active']),
        ]);
    }

    public function show(Doctor $doctor): View
    {
        app(DoctorScheduleService::class)->seedDefaultSchedule($doctor);

        $doctor->load(['department', 'schedules']);
        $doctor->loadCount(['patients', 'appointments']);

        $weeklyAvailability = app(DoctorScheduleService::class)->weeklySlots($doctor);

        return view('website.doctors.show', [
            'doctor' => $doctor,
            'weeklyAvailability' => $weeklyAvailability,
        ]);
    }

    public function bookAppointment(BookDoctorAppointmentRequest $request, Doctor $doctor): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $patient = $request->user()->patientProfile;

        abort_unless($patient, 403, 'Only patients can book appointments.');

        $appointment = DB::transaction(function () use ($doctor, $patient, $validated): Appointment {
            $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);

            return Appointment::create([
                'patient_id' => $patient->getKey(),
                'doctor_id' => $doctor->getKey(),
                'department_id' => $doctor->department_id,
                'appointment_date' => $validated['appointment_date'],
                'start_time' => $startTime->format('H:i'),
                'end_time' => $startTime->copy()->addMinutes(30)->format('H:i'),
                'status' => Appointment::STATUS_PENDING,
                'treatment' => $validated['treatment'],
                'notes' => $validated['notes'] ?? '',
                'phone_number' => $validated['phone_number'] ?? '',
                'fee' => $doctor->consultation_fee ?? 0,
            ]);
        });

        $appointment->load(['doctor', 'patient.user']);

        if ($appointment->patient?->user) {
            $appointment->patient->user->notify(new AppointmentBookedNotification($appointment));
        }

        if ($doctor->email) {
            Notification::route('mail', $doctor->email)
                ->notify(new AppointmentBookedNotification($appointment));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Appointment booked successfully. Medicare will confirm it shortly.',
                'appointment_id' => $appointment->getKey(),
            ], 201);
        }

        return redirect()
            ->route('website.booking.status', $appointment)
            ->with('status', 'Appointment booked successfully. Medicare will confirm it shortly.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ScopesVisibleRecords;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Mail\AppointmentApprovedMail;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AppointmentController extends Controller
{
    use ScopesVisibleRecords;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $baseQuery = $this->applyFilters(
            $this->visibleAppointmentsQuery($request->user()),
            $request,
            false,
        );

        $appointments = $this->applyFilters(
            $this->visibleAppointmentsQuery($request->user())
                ->select([
                    'id',
                    'patient_id',
                    'doctor_id',
                    'department_id',
                    'appointment_date',
                    'start_time',
                    'end_time',
                    'status',
                    'treatment',
                ])
                ->with([
                    Patient::relationConstraint('patient', ['name']),
                    Doctor::relationConstraint('doctor', ['name', 'department_id']),
                    'doctor.department:id,name',
                    'department:id,name',
                ]),
            $request,
            true,
        )
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('appointments._table_rows', ['appointments' => $appointments])->render(),
                'count' => $appointments->total(),
            ]);
        }

        return view('appointments.index', [
            'appointments' => $appointments,
            'doctors' => $this->assignableDoctors($request->user()),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
            'patients' => $this->assignablePatients($request->user()),
            'filters' => $request->only(['search', 'status', 'doctor_id', 'department_id', 'appointment_date']),
            'statusCounts' => [
                'all' => (clone $baseQuery)->count(),
                'confirmed' => (clone $baseQuery)->where('status', Appointment::STATUS_CONFIRMED)->count(),
                'pending' => (clone $baseQuery)->where('status', Appointment::STATUS_PENDING)->count(),
                'cancelled' => (clone $baseQuery)->where('status', Appointment::STATUS_CANCELLED)->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('appointments.create', [
            'appointment' => new Appointment,
            'patients' => $this->assignablePatients($request->user()),
            'doctors' => $this->assignableDoctors($request->user()),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $appointment = Appointment::create(
            $this->preparePayload($request->validated(), $request->user()),
        );

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('status', 'Appointment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment): View
    {
        abort_unless($this->canAccess($appointment, auth()->user()), 403);

        return view('appointments.show', [
            'appointment' => $appointment->load([
                Patient::relationConstraint('patient', ['name', 'doctor_id', 'email', 'phone']),
                'patient.'.Doctor::relationConstraint('doctor', ['name', 'department_id']),
                Doctor::relationConstraint('doctor', ['name', 'department_id']),
                'doctor.department:id,name',
                'department:id,name',
            ]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment, Request $request): View
    {
        abort_unless($this->canAccess($appointment, $request->user()), 403);

        return view('appointments.edit', [
            'appointment' => $appointment,
            'patients' => $this->assignablePatients($request->user()),
            'doctors' => $this->assignableDoctors($request->user()),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($this->canAccess($appointment, $request->user()), 403);

        $previousStatus = $appointment->status;

        $appointment->update(
            $this->preparePayload($request->validated(), $request->user()),
        );

        if (
            $appointment->status === Appointment::STATUS_CONFIRMED
            && $previousStatus !== Appointment::STATUS_CONFIRMED
        ) {
            $this->dispatchConfirmationEmail($appointment);
        }

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('status', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment, Request $request): RedirectResponse
    {
        abort_unless($this->canAccess($appointment, $request->user()), 403);

        $appointment->delete();

        return redirect()
            ->route('appointments.index')
            ->with('status', 'Appointment deleted successfully.');
    }

    /**
     * Quick status update (confirm / cancel) for doctors and admins.
     */
    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($this->canAccess($appointment, $request->user()), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled'],
        ]);

        $previousStatus = $appointment->status;
        $appointment->update(['status' => $validated['status']]);

        if (
            $validated['status'] === Appointment::STATUS_CONFIRMED
            && $previousStatus !== Appointment::STATUS_CONFIRMED
        ) {
            $this->dispatchConfirmationEmail($appointment);
        }

        $label = $validated['status'] === Appointment::STATUS_CONFIRMED ? 'confirmed' : 'cancelled';

        return redirect()
            ->back()
            ->with('status', "Appointment {$label} successfully.");
    }

    /**
     * Send the confirmation email to the patient — guaranteed once per appointment.
     * Uses the confirmation_email_sent_at timestamp as a lock, so re-confirming the
     * same appointment never produces duplicates. Failures are logged, never thrown.
     */
    private function dispatchConfirmationEmail(Appointment $appointment): void
    {
        $appointment->loadMissing([
            Patient::relationConstraint('patient', ['user_id', 'email', 'name']),
            'patient.user:id,email',
            Doctor::relationConstraint('doctor', ['name', 'specialty']),
        ]);

        // Idempotency guard — already sent before.
        if ($appointment->confirmation_email_sent_at !== null) {
            return;
        }

        $email = $appointment->patient?->email
            ?? $appointment->patient?->user?->email;

        if (blank($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Appointment confirmation email skipped: patient has no valid email.', [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
            ]);

            return;
        }

        try {
            Mail::to($email)->send(new AppointmentApprovedMail($appointment));

            $appointment->forceFill([
                'confirmation_email_sent_at' => now(),
            ])->save();

            Log::info('Appointment confirmation email dispatched.', [
                'appointment_id' => $appointment->id,
                'recipient' => $email,
            ]);
        } catch (Throwable $exception) {
            // Don't bubble — the appointment is already confirmed; only mail failed.
            Log::error('Failed to dispatch appointment confirmation email.', [
                'appointment_id' => $appointment->id,
                'recipient' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function applyFilters(Builder $query, Request $request, bool $includeStatus = true): Builder
    {
        return $query
            ->when(
                $request->filled('search'),
                fn (Builder $builder) => $builder->where(function (Builder $nested) use ($request): void {
                    $term = (string) $request->string('search');

                    $nested
                        ->where('treatment', 'like', "%{$term}%")
                        ->orWhereHas('patient', fn (Builder $patientQuery) => $patientQuery->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('doctor', fn (Builder $doctorQuery) => $doctorQuery->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('department', fn (Builder $deptQuery) => $deptQuery->where('name', 'like', "%{$term}%"));
                }),
            )
            ->when(
                $includeStatus && $request->filled('status'),
                fn (Builder $builder) => $builder->where('status', $request->string('status')),
            )
            ->when($request->filled('doctor_id'), fn (Builder $builder) => $builder->where('doctor_id', $request->integer('doctor_id')))
            ->when($request->filled('department_id'), fn (Builder $builder) => $builder->where('department_id', $request->integer('department_id')))
            ->when($request->filled('appointment_date'), fn (Builder $builder) => $builder->whereDate('appointment_date', $request->date('appointment_date')));
    }

    private function canAccess(Appointment $appointment, User $user): bool
    {
        return $user->isAdmin()
            || ($user->isDoctor() && $appointment->doctor_id === $user->doctorProfile?->getKey())
            || ($user->isPatient() && $appointment->patient_id === $user->patientProfile?->getKey());
    }

    private function preparePayload(array $data, User $user): array
    {
        if ($user->isDoctor() && $user->doctorProfile) {
            $data['doctor_id'] = $user->doctorProfile->getKey();
        }

        if ($user->isPatient() && $user->patientProfile) {
            $data['patient_id'] = $user->patientProfile->getKey();
            $data['status'] = Appointment::STATUS_PENDING;
        }

        if (empty($data['department_id'])) {
            $data['department_id'] = Doctor::query()->find($data['doctor_id'])?->department_id;
        }

        if (empty($data['fee'])) {
            $data['fee'] = Doctor::query()->find($data['doctor_id'])?->consultation_fee ?? 0;
        }

        return $data;
    }

    private function assignableDoctors(User $user)
    {
        if ($user->isDoctor() && $user->doctorProfile) {
            return Doctor::query()->whereKey($user->doctorProfile->getKey())->get(Doctor::columnsFor(['name']));
        }

        if ($user->isPatient()) {
            return Doctor::query()
                ->where('availability_status', Doctor::STATUS_AVAILABLE)
                ->orderBy('name')
                ->get(Doctor::columnsFor(['name']));
        }

        return Doctor::query()->orderBy('name')->get(Doctor::columnsFor(['name']));
    }

    private function assignablePatients(User $user)
    {
        if ($user->isPatient() && $user->patientProfile) {
            return Patient::query()->whereKey($user->patientProfile->getKey())->get(Patient::columnsFor(['name']));
        }

        return Patient::query()->orderBy('name')->get(Patient::columnsFor(['name']));
    }
}

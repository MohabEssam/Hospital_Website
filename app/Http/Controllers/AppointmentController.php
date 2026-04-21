<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $baseQuery = $this->applyFilters(
            $this->visibleAppointmentsQuery($request->user()),
            $request,
            false,
        );

        $appointments = $this->applyFilters(
            $this->visibleAppointmentsQuery($request->user())
                ->with(['patient', 'doctor.department']),
            $request,
            true,
        )
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->paginate(10)
            ->withQueryString();

        return view('appointments.index', [
            'appointments' => $appointments,
            'doctors' => $this->assignableDoctors($request->user()),
            'patients' => $this->assignablePatients($request->user()),
            'filters' => $request->only(['search', 'status', 'doctor_id', 'appointment_date']),
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
            'appointment' => new Appointment(),
            'patients' => $this->assignablePatients($request->user()),
            'doctors' => $this->assignableDoctors($request->user()),
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
            'appointment' => $appointment->load(['patient.doctor', 'doctor.department']),
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
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        abort_unless($this->canAccess($appointment, $request->user()), 403);

        $appointment->update(
            $this->preparePayload($request->validated(), $request->user()),
        );

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

    private function visibleAppointmentsQuery(User $user): Builder
    {
        $query = Appointment::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isDoctor() && $user->doctorProfile) {
            return $query->where('doctor_id', $user->doctorProfile->getKey());
        }

        if ($user->isPatient() && $user->patientProfile) {
            return $query->where('patient_id', $user->patientProfile->getKey());
        }

        return $query->whereRaw('1 = 0');
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
                        ->orWhereHas('doctor', fn (Builder $doctorQuery) => $doctorQuery->where('name', 'like', "%{$term}%"));
                }),
            )
            ->when(
                $includeStatus && $request->filled('status'),
                fn (Builder $builder) => $builder->where('status', $request->string('status')),
            )
            ->when($request->filled('doctor_id'), fn (Builder $builder) => $builder->where('doctor_id', $request->integer('doctor_id')))
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

        if (empty($data['fee'])) {
            $data['fee'] = Doctor::query()->find($data['doctor_id'])?->consultation_fee ?? 0;
        }

        return $data;
    }

    private function assignableDoctors(User $user)
    {
        if ($user->isDoctor() && $user->doctorProfile) {
            return Doctor::query()->whereKey($user->doctorProfile->getKey())->get();
        }

        if ($user->isPatient()) {
            return Doctor::query()
                ->where('availability_status', Doctor::STATUS_AVAILABLE)
                ->orderBy('name')
                ->get();
        }

        return Doctor::query()->orderBy('name')->get();
    }

    private function assignablePatients(User $user)
    {
        if ($user->isPatient() && $user->patientProfile) {
            return Patient::query()->whereKey($user->patientProfile->getKey())->get();
        }

        return Patient::query()->orderBy('name')->get();
    }
}

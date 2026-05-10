<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $patients = $this->visiblePatientsQuery($request->user())
            ->with(['doctor.department'])
            ->withCount('appointments')
            ->when($request->filled('period'), function (Builder $query) use ($request): void {
                match ((string) $request->string('period')) {
                    'month' => $query->whereBetween('check_in_date', [
                        today()->startOfMonth()->toDateString(),
                        today()->endOfMonth()->toDateString(),
                    ]),
                    'last_30_days' => $query->whereBetween('check_in_date', [
                        today()->subDays(29)->toDateString(),
                        today()->toDateString(),
                    ]),
                    default => null,
                };
            })
            ->when(
                $request->filled('search'),
                fn (Builder $query) => $query->where(function (Builder $nested) use ($request): void {
                    $term = (string) $request->string('search');

                    $nested
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('patient_code', 'like', "%{$term}%")
                        ->orWhere('treatment', 'like', "%{$term}%")
                        ->orWhereHas('doctor', fn (Builder $doctorQuery) => $doctorQuery->where('name', 'like', "%{$term}%"));
                }),
            )
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->when($request->filled('treatment'), fn (Builder $query) => $query->where('treatment', $request->string('treatment')))
            ->when(
                $request->filled('sort'),
                function (Builder $query) use ($request): void {
                    match ((string) $request->string('sort')) {
                        'name' => $query->orderBy('name'),
                        'code' => $query->orderBy('patient_code'),
                        'age' => $query->orderBy('date_of_birth'),
                        default => $query->orderByDesc('id'),
                    };
                },
                fn (Builder $query) => $query->orderByDesc('id'),
            )
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', [
            'patients' => $patients,
            'doctors' => Doctor::query()->orderBy('name')->get(['id', 'name']),
            'treatmentOptions' => $this->visiblePatientsQuery($request->user())
                ->whereNotNull('treatment')
                ->select('treatment')
                ->distinct()
                ->orderBy('treatment')
                ->pluck('treatment'),
            'filters' => $request->only(['search', 'status', 'treatment', 'period', 'sort']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('patients.create', [
            'doctors' => Doctor::query()->orderBy('name')->get(),
            'patient' => new Patient(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        $patient = Patient::create($request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Patient    successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): View
    {
        abort_unless($this->canView($patient, auth()->user()), 403);

        $patient->load([
            'doctor.department',
            'appointments.doctor.department',
        ]);

        return view('patients.show', [
            'patient' => $patient,
            'appointments' => $patient->appointments->sortByDesc(
                fn ($appointment) => $appointment->appointment_date?->format('Y-m-d').$appointment->start_time,
            ),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient): View
    {
        return view('patients.edit', [
            'patient' => $patient->load('doctor'),
            'doctors' => Doctor::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $patient->update($request->validated());

        return redirect()
            ->route('patients.show', $patient)
            ->with('status', 'Patient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('status', 'Patient deleted successfully.');
    }

    private function visiblePatientsQuery(User $user): Builder
    {
        $query = Patient::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isDoctor() && $user->doctorProfile) {
            return $query->where('doctor_id', $user->doctorProfile->getKey());
        }

        if ($user->isPatient()) {
            return $query->where('user_id', $user->getKey());
        }

        return $query->whereRaw('1 = 0');
    }

    private function canView(Patient $patient, User $user): bool
    {
        return $user->isAdmin()
            || ($user->isDoctor() && $user->doctorProfile?->is($patient->doctor))
            || ($user->isPatient() && $patient->user_id === $user->getKey());
    }
}

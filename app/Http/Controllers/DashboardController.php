<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\ServiceBooking;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $appointmentsQuery = $this->visibleAppointmentsQuery($user);
        $patientsQuery = $this->visiblePatientsQuery($user);

        // Revenue/income excludes cancelled appointments. No fake expense figure.
        $nonCancelled = fn (Builder $q): Builder => $q->where('status', '!=', Appointment::STATUS_CANCELLED);

        $stats = [
            'patients' => (clone $patientsQuery)->count(),
            'doctors' => $user->isAdmin() ? Doctor::query()->count() : 1,
            'appointments' => (clone $appointmentsQuery)->count(),
            'available_doctors' => $user->isAdmin()
                ? Doctor::query()->where('availability_status', Doctor::STATUS_AVAILABLE)->count()
                : (int) ($user->doctorProfile?->isAvailable() ?? false),
            'departments' => $user->isAdmin() ? Department::query()->count() : 0,
            'patients_this_month' => (clone $patientsQuery)
                ->whereBetween('created_at', [today()->startOfMonth(), today()->endOfMonth()])
                ->count(),
            'appointments_today' => (clone $appointmentsQuery)
                ->whereDate('appointment_date', today())
                ->count(),
            'pending_appointments' => (clone $appointmentsQuery)
                ->where('status', Appointment::STATUS_PENDING)
                ->count(),
            'pending_service_bookings' => $user->isAdmin()
                ? ServiceBooking::query()->where('status', ServiceBooking::STATUS_PENDING)->count()
                : 0,
        ];

        // --- Revenue (real, confirmed/pending only) ---
        $chartLabels = collect(range(0, 6))
            ->map(fn (int $offset) => today()->subDays(6 - $offset))
            ->values();

        $incomeSeries = $chartLabels
            ->map(fn ($date) => (float) (clone $appointmentsQuery)
                ->tap($nonCancelled)
                ->whereDate('appointment_date', $date)
                ->sum('fee'))
            ->all();

        // "Pending revenue" = fee of bookings not yet confirmed (real, not fabricated)
        $pendingSeries = $chartLabels
            ->map(fn ($date) => (float) (clone $appointmentsQuery)
                ->where('status', Appointment::STATUS_PENDING)
                ->whereDate('appointment_date', $date)
                ->sum('fee'))
            ->all();

        $monthlyRevenuePeriods = collect(range(0, 3))
            ->map(fn (int $offset) => today()->copy()->startOfWeek()->subWeeks(3 - $offset));

        $monthlyIncomeSeries = $monthlyRevenuePeriods
            ->map(function ($startOfWeek) use ($appointmentsQuery, $nonCancelled): float {
                return (float) (clone $appointmentsQuery)
                    ->tap($nonCancelled)
                    ->whereBetween('appointment_date', [
                        $startOfWeek->copy()->toDateString(),
                        $startOfWeek->copy()->endOfWeek()->toDateString(),
                    ])
                    ->sum('fee');
            })
            ->all();

        $monthlyPendingSeries = $monthlyRevenuePeriods
            ->map(function ($startOfWeek) use ($appointmentsQuery): float {
                return (float) (clone $appointmentsQuery)
                    ->where('status', Appointment::STATUS_PENDING)
                    ->whereBetween('appointment_date', [
                        $startOfWeek->copy()->toDateString(),
                        $startOfWeek->copy()->endOfWeek()->toDateString(),
                    ])
                    ->sum('fee');
            })
            ->all();

        $yearlyRevenuePeriods = collect(range(0, 5))
            ->map(fn (int $offset) => today()->copy()->startOfMonth()->subMonths(5 - $offset));

        $yearlyIncomeSeries = $yearlyRevenuePeriods
            ->map(function ($month) use ($appointmentsQuery, $nonCancelled): float {
                return (float) (clone $appointmentsQuery)
                    ->tap($nonCancelled)
                    ->whereBetween('appointment_date', [
                        $month->copy()->toDateString(),
                        $month->copy()->endOfMonth()->toDateString(),
                    ])
                    ->sum('fee');
            })
            ->all();

        $yearlyPendingSeries = $yearlyRevenuePeriods
            ->map(function ($month) use ($appointmentsQuery): float {
                return (float) (clone $appointmentsQuery)
                    ->where('status', Appointment::STATUS_PENDING)
                    ->whereBetween('appointment_date', [
                        $month->copy()->toDateString(),
                        $month->copy()->endOfMonth()->toDateString(),
                    ])
                    ->sum('fee');
            })
            ->all();

        // --- Appointments per day (last 7 days) ---
        $appointmentsPerDaySeries = $chartLabels
            ->map(fn ($date) => (int) (clone $appointmentsQuery)
                ->whereDate('appointment_date', $date)
                ->count())
            ->all();

        // --- Status distribution (real) ---
        $statusDistribution = [
            'confirmed' => (clone $appointmentsQuery)->where('status', Appointment::STATUS_CONFIRMED)->count(),
            'pending' => (clone $appointmentsQuery)->where('status', Appointment::STATUS_PENDING)->count(),
            'cancelled' => (clone $appointmentsQuery)->where('status', Appointment::STATUS_CANCELLED)->count(),
        ];

        $ageGroups = $this->patientAgeGroups($patientsQuery);

        $departmentDistribution = Department::query()
            ->select(['id', 'name'])
            ->withCount('doctors')
            ->orderByDesc('doctors_count')
            ->take(4)
            ->get();

        $topDoctors = Doctor::query()
            ->select(['id', 'doctor_code', 'department_id', 'name', 'availability_status'])
            ->with('department:id,name')
            ->withCount(['patients', 'appointments'])
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();

        $recentAppointments = (clone $appointmentsQuery)
            ->select(['id', 'patient_id', 'doctor_id', 'appointment_date', 'start_time', 'status', 'treatment'])
            ->with([
                Patient::relationConstraint('patient', ['name']),
                Doctor::relationConstraint('doctor', ['name']),
            ])
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->take(6)
            ->get();

        $todaySchedule = (clone $appointmentsQuery)
            ->select(['id', 'patient_id', 'doctor_id', 'appointment_date', 'start_time', 'status', 'treatment'])
            ->with([
                Patient::relationConstraint('patient', ['name']),
                Doctor::relationConstraint('doctor', ['name']),
            ])
            ->whereDate('appointment_date', today())
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $dateStripDays = collect(range(0, 6))
            ->map(fn (int $offset) => today()->copy()->startOfWeek()->addDays($offset));

        $dateStripCounts = (clone $appointmentsQuery)
            ->whereBetween('appointment_date', [
                $dateStripDays->first()->toDateString(),
                $dateStripDays->last()->toDateString(),
            ])
            ->get(['appointment_date'])
            ->countBy(fn (Appointment $appointment) => $appointment->appointment_date->toDateString());

        $appointmentDateStrip = $dateStripDays
            ->map(fn ($day) => [
                'label' => $day->format('D'),
                'day' => $day->format('d'),
                'full_date' => $day->format('Y-m-d'),
                'is_today' => $day->isToday(),
                'count' => (int) ($dateStripCounts[$day->toDateString()] ?? 0),
            ])
            ->all();

        $miniCalendarCounts = (clone $appointmentsQuery)
            ->whereBetween('appointment_date', [
                today()->copy()->startOfMonth()->toDateString(),
                today()->copy()->endOfMonth()->toDateString(),
            ])
            ->get(['appointment_date'])
            ->countBy(fn (Appointment $appointment) => $appointment->appointment_date->toDateString());

        $doctor = $user->isDoctor() ? $user->doctorProfile : null;

        return view('dashboard.index', [
            'stats' => $stats,
            'doctor' => $doctor,
            'patientAgeGroups' => $ageGroups,
            'revenueLabels' => $chartLabels->map->format('D')->all(),
            'incomeSeries' => $incomeSeries,
            'pendingSeries' => $pendingSeries,
            'appointmentsPerDaySeries' => $appointmentsPerDaySeries,
            'statusDistribution' => $statusDistribution,
            'revenueDatasets' => [
                'week' => [
                    'labels' => $chartLabels->map->format('D')->all(),
                    'income' => $incomeSeries,
                    'pending' => $pendingSeries,
                ],
                'month' => [
                    'labels' => $monthlyRevenuePeriods->map(fn ($period) => $period->format('\W\e\e\k W'))->all(),
                    'income' => $monthlyIncomeSeries,
                    'pending' => $monthlyPendingSeries,
                ],
                'year' => [
                    'labels' => $yearlyRevenuePeriods->map->format('M')->all(),
                    'income' => $yearlyIncomeSeries,
                    'pending' => $yearlyPendingSeries,
                ],
            ],
            'departmentDistribution' => $departmentDistribution,
            'topDoctors' => $topDoctors,
            'recentAppointments' => $recentAppointments,
            'todaySchedule' => $todaySchedule,
            'appointmentDateStrip' => $appointmentDateStrip,
            'miniCalendarCounts' => $miniCalendarCounts,
        ]);
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

    private function visiblePatientsQuery(User $user): Builder
    {
        $query = Patient::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isDoctor() && $user->doctorProfile) {
            return $query->whereIn(
                'id',
                Appointment::query()
                    ->where('doctor_id', $user->doctorProfile->getKey())
                    ->select('patient_id')
                    ->distinct(),
            );
        }

        if ($user->isPatient()) {
            return $query->where('user_id', $user->getKey());
        }

        return $query->whereRaw('1 = 0');
    }

    private function patientAgeGroups(Builder $query): array
    {
        $groups = ['child' => 0, 'adult' => 0, 'elderly' => 0];

        (clone $query)
            ->get(['id', 'date_of_birth', 'age'])
            ->each(function (Patient $patient) use (&$groups): void {
                $group = $patient->ageGroup();

                if (array_key_exists($group, $groups)) {
                    $groups[$group]++;
                }
            });

        return $groups;
    }

    /**
     * JSON endpoint powering the "Patient Overview (by Age Stages)" card.
     * Respects role scoping via visiblePatientsQuery() and filters by range.
     */
    public function getPatientOverview(Request $request): JsonResponse
    {
        $range = in_array($request->string('range')->toString(), ['current', 'quarter', 'year'], true)
            ? $request->string('range')->toString()
            : 'current';

        $query = $this->visiblePatientsQuery($request->user());

        if ($range === 'quarter') {
            $query->whereBetween('created_at', [today()->startOfQuarter(), today()->endOfQuarter()]);
        } elseif ($range === 'year') {
            $query->whereBetween('created_at', [today()->startOfYear(), today()->endOfYear()]);
        }

        $groups = $this->patientAgeGroups($query);

        return response()->json([
            'range' => $range,
            'child' => $groups['child'],
            'adult' => $groups['adult'],
            'elderly' => $groups['elderly'],
            'total' => array_sum($groups),
        ]);
    }

    public function updateAvailability(Request $request): RedirectResponse
    {
        $doctor = $request->user()->doctorProfile;

        abort_unless($doctor, 403);

        $validated = $request->validate([
            'availability_status' => ['required', Rule::in(Doctor::availabilityOptions())],
        ]);

        $doctor->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Availability updated to '.ucfirst($doctor->availability_status).'.');
    }
}

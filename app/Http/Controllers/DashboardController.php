<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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

        $stats = [
            'patients' => (clone $patientsQuery)->count(),
            'doctors' => Doctor::query()->count(),
            'appointments' => (clone $appointmentsQuery)->count(),
            'available_doctors' => Doctor::query()
                ->where('availability_status', Doctor::STATUS_AVAILABLE)
                ->count(),
            'departments' => Department::query()->count(),
            'patients_this_month' => (clone $patientsQuery)
                ->whereBetween('created_at', [today()->startOfMonth(), today()->endOfMonth()])
                ->count(),
            'appointments_today' => (clone $appointmentsQuery)
                ->whereDate('appointment_date', today())
                ->count(),
        ];

        $chartLabels = collect(range(0, 6))
            ->map(fn (int $offset) => today()->subDays(6 - $offset))
            ->values();

        $incomeSeries = $chartLabels
            ->map(fn ($date) => (float) (clone $appointmentsQuery)
                ->whereDate('appointment_date', $date)
                ->sum('fee'))
            ->all();

        $expenseSeries = collect($incomeSeries)
            ->map(fn (float $value) => round($value * 0.38, 2))
            ->all();

        $monthlyRevenuePeriods = collect(range(0, 3))
            ->map(fn (int $offset) => today()->copy()->startOfWeek()->subWeeks(3 - $offset));

        $monthlyIncomeSeries = $monthlyRevenuePeriods
            ->map(function ($startOfWeek) use ($appointmentsQuery): float {
                return (float) (clone $appointmentsQuery)
                    ->whereBetween('appointment_date', [
                        $startOfWeek->copy()->toDateString(),
                        $startOfWeek->copy()->endOfWeek()->toDateString(),
                    ])
                    ->sum('fee');
            })
            ->all();

        $monthlyExpenseSeries = collect($monthlyIncomeSeries)
            ->map(fn (float $value) => round($value * 0.38, 2))
            ->all();

        $yearlyRevenuePeriods = collect(range(0, 5))
            ->map(fn (int $offset) => today()->copy()->startOfMonth()->subMonths(5 - $offset));

        $yearlyIncomeSeries = $yearlyRevenuePeriods
            ->map(function ($month) use ($appointmentsQuery): float {
                return (float) (clone $appointmentsQuery)
                    ->whereBetween('appointment_date', [
                        $month->copy()->toDateString(),
                        $month->copy()->endOfMonth()->toDateString(),
                    ])
                    ->sum('fee');
            })
            ->all();

        $yearlyExpenseSeries = collect($yearlyIncomeSeries)
            ->map(fn (float $value) => round($value * 0.38, 2))
            ->all();

        $ageGroups = ['child' => 0, 'adult' => 0, 'elderly' => 0];

        (clone $patientsQuery)
            ->get(['id', 'date_of_birth'])
            ->each(function (Patient $patient) use (&$ageGroups): void {
                $group = $patient->ageGroup();

                if (array_key_exists($group, $ageGroups)) {
                    $ageGroups[$group]++;
                }
            });

        $departmentDistribution = Department::query()
            ->withCount('doctors')
            ->orderByDesc('doctors_count')
            ->take(4)
            ->get();

        $topDoctors = Doctor::query()
            ->with('department')
            ->withCount(['patients', 'appointments'])
            ->orderByDesc('appointments_count')
            ->take(5)
            ->get();

        $recentAppointments = (clone $appointmentsQuery)
            ->with(['patient', 'doctor.department'])
            ->orderByDesc('appointment_date')
            ->orderBy('start_time')
            ->take(6)
            ->get();

        $todaySchedule = (clone $appointmentsQuery)
            ->with(['patient', 'doctor.department'])
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
            'expenseSeries' => $expenseSeries,
            'revenueDatasets' => [
                'week' => [
                    'labels' => $chartLabels->map->format('D')->all(),
                    'income' => $incomeSeries,
                    'expense' => $expenseSeries,
                ],
                'month' => [
                    'labels' => $monthlyRevenuePeriods->map(fn ($period) => $period->format('\W\e\e\k W'))->all(),
                    'income' => $monthlyIncomeSeries,
                    'expense' => $monthlyExpenseSeries,
                ],
                'year' => [
                    'labels' => $yearlyRevenuePeriods->map->format('M')->all(),
                    'income' => $yearlyIncomeSeries,
                    'expense' => $yearlyExpenseSeries,
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
            ->with('status', 'Availability updated to ' . ucfirst($doctor->availability_status) . '.');
    }
}

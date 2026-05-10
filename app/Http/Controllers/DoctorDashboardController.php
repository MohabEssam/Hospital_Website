<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\DoctorScheduleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    public function __invoke(Request $request, DoctorScheduleService $scheduleService): View
    {
        $doctor = $request->user()->doctorProfile;

        abort_unless($doctor, 404, 'Doctor profile not found.');

        $scheduleService->seedDefaultSchedule($doctor);

        $doctor->load(['department', 'schedules']);
        $doctor->loadCount([
            'appointments',
            'appointments as upcoming_appointments_count' => fn ($query) => $query
                ->whereDate('appointment_date', '>=', today())
                ->where('status', '!=', Appointment::STATUS_CANCELLED),
        ]);

        $upcomingAppointments = $doctor->appointments()
            ->with('patient')
            ->whereDate('appointment_date', '>=', today())
            ->where('status', '!=', Appointment::STATUS_CANCELLED)
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->take(8)
            ->get();

        $totalPatients = $doctor->appointments()
            ->distinct('patient_id')
            ->count('patient_id');

        $weeklySchedule = $doctor->schedules
            ->sortBy(['day_of_week', 'start_time'])
            ->groupBy('day_of_week');

        return view('doctor.dashboard', [
            'doctor' => $doctor,
            'upcomingAppointments' => $upcomingAppointments,
            'totalPatients' => $totalPatients,
            'weeklySchedule' => $weeklySchedule,
        ]);
    }
}

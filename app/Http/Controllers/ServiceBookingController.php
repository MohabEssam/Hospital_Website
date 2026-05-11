<?php

namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceBookingController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status', 'all');

        $query = ServiceBooking::query()
            ->select([
                'id',
                'patient_id',
                'patient_care_service_id',
                'booking_date',
                'booking_time',
                'phone_number',
                'status',
            ])
            ->with([
                'patient:id,user_id,name',
                'patient.user:id,email',
                'service:id,name',
            ]);

        if ($statusFilter !== 'all' && in_array($statusFilter, [ServiceBooking::STATUS_PENDING, ServiceBooking::STATUS_CONFIRMED, ServiceBooking::STATUS_REJECTED], true)) {
            $query->where('status', $statusFilter);
        }

        $bookings = $query
            ->orderByDesc('booking_date')
            ->orderBy('booking_time')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => ServiceBooking::count(),
            'pending' => ServiceBooking::where('status', ServiceBooking::STATUS_PENDING)->count(),
            'confirmed' => ServiceBooking::where('status', ServiceBooking::STATUS_CONFIRMED)->count(),
            'rejected' => ServiceBooking::where('status', ServiceBooking::STATUS_REJECTED)->count(),
        ];

        return view('service-bookings.index', [
            'bookings' => $bookings,
            'statusFilter' => $statusFilter,
            'counts' => $counts,
        ]);
    }

    public function updateStatus(Request $request, ServiceBooking $serviceBooking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,rejected'],
        ]);

        $serviceBooking->update(['status' => $validated['status']]);

        return redirect()
            ->back()
            ->with('status', 'Service booking ' . $validated['status'] . ' successfully.');
    }
}

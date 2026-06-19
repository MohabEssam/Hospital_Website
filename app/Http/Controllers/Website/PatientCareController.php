<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookServiceRequest;
use App\Models\PatientCareService;
use App\Models\ServiceBooking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PatientCareController extends Controller
{
    public function index(): View
    {
        return view('website.patient-care.index', [
            'services' => PatientCareService::query()
                ->active()
                ->orderBy('sort_order')
                ->get(PatientCareService::columnsFor([
                    'name',
                    'description',
                    'image',
                    'icon_class',
                    'is_bookable',
                    'is_active',
                    'sort_order',
                ])),
        ]);
    }

    public function show(PatientCareService $service): View
    {
        $allServices = PatientCareService::query()
            ->active()
            ->orderBy('sort_order')
            ->get(PatientCareService::columnsFor(['name', 'icon_class']));

        return view('website.patient-care.show', [
            'service' => $service,
            'allServices' => $allServices,
        ]);
    }

    public function storeBooking(BookServiceRequest $request, PatientCareService $service): RedirectResponse
    {
        $patient = $request->user()->patientProfile;

        abort_unless($patient, 403, 'A patient profile is required to book a service.');

        ServiceBooking::create([
            'patient_id' => $patient->getKey(),
            'patient_care_service_id' => $service->getKey(),
            'booking_date' => $request->validated('booking_date'),
            'booking_time' => $request->validated('booking_time'),
            'phone_number' => $request->validated('phone_number'),
            'notes' => $request->validated('notes'),
            'status' => ServiceBooking::STATUS_PENDING,
        ]);

        return redirect()
            ->route('website.patient-care.show', $service)
            ->with('status', 'Your booking for '.$service->name.' has been submitted successfully. We will confirm it shortly.');
    }
}

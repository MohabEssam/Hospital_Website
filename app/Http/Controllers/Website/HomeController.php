<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientCareService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('website.home', [
            'departments' => Department::query()
                ->active()
                ->withCount('doctors')
                ->with(['doctors' => function ($q) {
                    $q->with('department')->take(4)->orderBy('name');
                }])
                ->orderBy('name')
                ->get(),
            'doctors' => Doctor::query()
                ->with('department')
                ->where('availability_status', Doctor::STATUS_AVAILABLE)
                ->orderBy('name')
                ->get(),
            'stats' => [
                'departments' => Department::query()->active()->count(),
                'doctors' => Doctor::query()->count(),
                'patients' => Patient::query()->count(),
            ],
            'patientCareServices' => PatientCareService::query()
                ->active()
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}

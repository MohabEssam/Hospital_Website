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
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'icon', 'is_active']),
            'doctors' => Doctor::query()
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
                ->get(['id', 'name', 'slug', 'description', 'image', 'icon_class', 'is_active', 'sort_order']),
        ]);
    }
}

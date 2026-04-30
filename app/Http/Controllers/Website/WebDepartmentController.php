<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Contracts\View\View;

class WebDepartmentController extends Controller
{
    public function index(): View
    {
        return view('website.departments.index', [
            'departments' => Department::query()
                ->active()
                ->withCount('doctors')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function show(Department $department): View
    {
        $department->load(['doctors' => function ($query) {
            $query->with('department')->orderBy('name');
        }]);

        $allDepartments = Department::query()
            ->active()
            ->withCount('doctors')
            ->orderBy('name')
            ->get();

        return view('website.departments.show', [
            'department'     => $department,
            'allDepartments' => $allDepartments,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebDepartmentController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $departments = Department::query()
            ->select(['id', 'name', 'slug', 'description', 'services', 'icon', 'hero_image', 'is_active'])
            ->active()
            ->withCount('doctors')
            ->when(
                $request->filled('search'),
                fn (Builder $query) => $query->where(function (Builder $nested) use ($request): void {
                    $term = (string) $request->string('search');

                    $nested
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('description', 'like', "%{$term}%")
                        ->orWhere('services', 'like', "%{$term}%");
                }),
            )
            ->orderBy('name')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('website.departments._grid', ['departments' => $departments])->render(),
                'count' => $departments->count(),
            ]);
        }

        return view('website.departments.index', [
            'departments' => $departments,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Department $department): View
    {
        $department->load(['doctors' => function ($query) {
            $query
                ->select(Doctor::columnsFor([
                    'department_id',
                    'name',
                    'specialty',
                    'availability_status',
                    'avatar',
                ]))
                ->orderBy('name');
        }]);

        $allDepartments = Department::query()
            ->select(['id', 'name', 'slug', 'is_active', 'icon'])
            ->active()
            ->withCount('doctors')
            ->orderBy('name')
            ->get();

        return view('website.departments.show', [
            'department' => $department,
            'allDepartments' => $allDepartments,
        ]);
    }
}

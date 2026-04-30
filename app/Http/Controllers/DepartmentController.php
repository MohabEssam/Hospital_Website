<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('departments.index', [
            'departments' => Department::query()
                ->withCount('doctors')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('departments.create', [
            'department' => new Department(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $data = $this->handleImageUploads($request->validated(), $request);
        $department = Department::create($data);

        return redirect()
            ->route('departments.show', $department)
            ->with('status', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department): View
    {
        $department->load([
            'doctors' => fn ($query) => $query
                ->withCount(['patients', 'appointments'])
                ->orderBy('name'),
        ]);

        return view('departments.show', [
            'department' => $department,
            'otherDepartments' => Department::query()
                ->whereKeyNot($department->getKey())
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department): View
    {
        return view('departments.edit', [
            'department' => $department,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $data = $this->handleImageUploads($request->validated(), $request, $department);
        $department->update($data);

        return redirect()
            ->route('departments.show', $department)
            ->with('status', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department): RedirectResponse
    {
        foreach (['icon', 'hero_image', 'sidebar_image'] as $field) {
            if ($department->{$field}) {
                Storage::disk('public')->delete($department->{$field});
            }
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('status', 'Department deleted successfully.');
    }

    /**
     * Handle image file uploads for store/update operations.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function handleImageUploads(array $data, StoreDepartmentRequest|UpdateDepartmentRequest $request, ?Department $department = null): array
    {
        $imageFields = ['icon', 'hero_image', 'sidebar_image'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                if ($department && $department->{$field}) {
                    Storage::disk('public')->delete($department->{$field});
                }

                $data[$field] = $request->file($field)->store('departments', 'public');
            } else {
                unset($data[$field]);
            }
        }

        return $data;
    }
}

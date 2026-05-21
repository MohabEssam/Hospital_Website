<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffUserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StaffUserController extends Controller
{
    public function index(): View
    {
        return view('staff-users.index', [
            'users' => User::query()
                ->whereIn('role', [
                    User::ROLE_ADMIN,
                    User::ROLE_LAB,
                    User::ROLE_PHARMACY,
                    User::ROLE_SCAN_CENTER,
                ])
                ->orderBy('role')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('staff-users.create', [
            'roles' => [
                User::ROLE_ADMIN => 'Admin',
                User::ROLE_LAB => 'Lab',
                User::ROLE_PHARMACY => 'Pharmacy',
                User::ROLE_SCAN_CENTER => 'Scan Center',
            ],
        ]);
    }

    public function store(StoreStaffUserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()
            ->route('staff-users.index')
            ->with('status', "User {$user->public_id} created successfully.");
    }
}

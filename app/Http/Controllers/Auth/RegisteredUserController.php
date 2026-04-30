<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => User::ROLE_PATIENT,
        ]);

        Patient::create([
            'user_id' => $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
            'age' => $request->validated('age'),
            'status' => Patient::STATUS_NEW,
            'check_in_date' => today(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}

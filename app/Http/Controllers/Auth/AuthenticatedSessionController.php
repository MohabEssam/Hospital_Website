<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->credentials(), $request->remember())) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if ($user->isLab()) {
            return $this->redirectStaffToDashboard($request, 'lab.dashboard');
        }

        if ($user->isScanCenter()) {
            return $this->redirectStaffToDashboard($request, 'scan-center.dashboard');
        }

        if ($user->isPharmacy()) {
            return $this->redirectStaffToDashboard($request, 'pharmacy.dashboard');
        }

        if ($user->isDoctor()) {
            return redirect()->intended(route('dashboard'));
        }

        if ($user->isPatient()) {
            return redirect()->intended(route('patient.dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }

    private function redirectStaffToDashboard(Request $request, string $routeName): RedirectResponse
    {
        $dashboardUrl = route($routeName);
        $intendedUrl = $request->session()->pull('url.intended');

        if (is_string($intendedUrl) && $this->urlsHaveSamePath($intendedUrl, $dashboardUrl)) {
            return redirect()->to($intendedUrl);
        }

        return redirect()->to($dashboardUrl);
    }

    private function urlsHaveSamePath(string $firstUrl, string $secondUrl): bool
    {
        $firstPath = parse_url($firstUrl, PHP_URL_PATH) ?: '/';
        $secondPath = parse_url($secondUrl, PHP_URL_PATH) ?: '/';

        return rtrim($firstPath, '/') === rtrim($secondPath, '/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

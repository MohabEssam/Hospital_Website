<?php

namespace App\Http\Controllers;

use App\Services\PatientLookupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientLookupController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()?->isAdmin() || auth()->user()?->isReception(), 403);

        return view('patients.lookup');
    }

    public function search(Request $request, PatientLookupService $lookup): JsonResponse
    {
        $validated = $request->validate([
            'context' => ['required', Rule::in(PatientLookupService::contexts())],
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $patients = $lookup->search(
            $request->user(),
            $validated['context'],
            (string) ($validated['q'] ?? ''),
        );

        return response()->json([
            'results' => $patients->map(fn ($patient): array => [
                'patient_code' => $patient->patient_code,
                'name' => $patient->name,
                'phone' => $patient->phone,
                'email' => $patient->email,
                'age' => $patient->age(),
                'gender' => $patient->gender,
                'profile_url' => $request->user()->isReception()
                    ? route('reception.patients.show', $patient)
                    : route('patients.show', $patient),
            ])->values(),
        ]);
    }
}

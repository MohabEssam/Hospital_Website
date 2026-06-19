<?php

namespace App\Http\Requests;

use App\Models\Prescription;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DispensePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isAdmin() || $user->isPharmacy());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([Prescription::STATUS_DISPENSED, Prescription::STATUS_CANCELLED])],
            'dispensed_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', Prescription::STATUS_DISPENSED),
            'dispensed_at' => $this->input('dispensed_at') ?: now()->toDateTimeString(),
        ]);
    }
}

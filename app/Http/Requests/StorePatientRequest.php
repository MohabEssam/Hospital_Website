<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('patients', 'email')],
            'phone' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'gender' => ['nullable', 'string', 'max:255'],
            'check_in_date' => ['nullable', 'date'],
            'treatment' => ['nullable', 'string', 'max:255'],
            'room_number' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(Patient::statusOptions())],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'avatar_path' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $dateOfBirth = $this->input('date_of_birth');
        $age = $this->input('age');

        if (blank($dateOfBirth) && filled($age) && is_numeric($age)) {
            $dateOfBirth = Carbon::today()
                ->subYears((int) $age)
                ->toDateString();
        }

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => filled($this->input('email')) ? strtolower(trim((string) $this->input('email'))) : null,
            'date_of_birth' => $dateOfBirth,
        ]);
    }
}

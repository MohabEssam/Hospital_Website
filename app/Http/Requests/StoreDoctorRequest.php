<?php

namespace App\Http\Requests;

use App\Models\Doctor;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDoctorRequest extends FormRequest
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
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'doctor_code' => ['nullable', 'string', 'max:255', Rule::unique('doctors', 'doctor_code')],
            'email' => ['required', 'email', 'max:255', Rule::unique('doctors', 'email'), Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'specialty' => ['required', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'availability_status' => ['required', Rule::in(Doctor::availabilityOptions())],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:70'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'doctor_code' => filled($this->input('doctor_code')) ? strtoupper(trim((string) $this->input('doctor_code'))) : null,
            'email' => filled($this->input('email')) ? strtolower(trim((string) $this->input('email'))) : null,
            'phone' => trim((string) $this->input('phone')),
        ]);
    }
}

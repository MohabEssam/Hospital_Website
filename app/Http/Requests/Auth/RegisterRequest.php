<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'age' => ['required_without:date_of_birth', 'nullable', 'integer', 'min:1', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $dateOfBirth = $this->input('date_of_birth');
        $age = $this->input('age');

        if (blank($age) && filled($dateOfBirth)) {
            $age = Carbon::parse($dateOfBirth)->age;
        }

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(trim((string) $this->input('email'))),
            'age' => $age,
        ]);
    }
}

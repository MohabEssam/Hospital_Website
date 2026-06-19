<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('departments', 'name')],
            'description' => ['nullable', 'string'],
            'services' => ['nullable', 'array'],
            'services.*' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'sidebar_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'services' => $this->normalizeServices(),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeServices(): array
    {
        if (is_array($this->input('services'))) {
            return collect($this->input('services'))
                ->map(fn ($service) => trim((string) $service))
                ->filter()
                ->values()
                ->all();
        }

        return collect(preg_split('/\r\n|\r|\n/', (string) $this->input('services', '')))
            ->map(fn ($service) => trim((string) $service))
            ->filter()
            ->values()
            ->all();
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScanResultRequest extends FormRequest
{
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
            'findings' => ['nullable', 'string', 'max:10000'],
            'impression' => ['nullable', 'string', 'max:10000', 'required_without:images'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:10240'],
            'status' => ['required', Rule::in(['preliminary', 'final'])],
            'resulted_at' => ['nullable', 'date'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'final'),
            'resulted_at' => $this->input('resulted_at') ?: now()->toDateTimeString(),
        ]);
    }
}

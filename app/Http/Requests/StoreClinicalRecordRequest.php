<?php

namespace App\Http\Requests;

use App\Models\Diagnosis;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClinicalRecordRequest extends FormRequest
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
            'appointment_id' => ['nullable', 'exists:appointments,id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:4000'],
            'symptoms' => ['nullable', 'string', 'max:4000'],
            'notes' => ['nullable', 'string', 'max:4000'],
            'status' => ['required', Rule::in([Diagnosis::STATUS_ACTIVE, Diagnosis::STATUS_RESOLVED])],
            'diagnosed_at' => ['nullable', 'date'],

            'lab_requests' => ['nullable', 'array', 'max:20'],
            'lab_requests.*.test_name' => ['nullable', 'string', 'max:255'],
            'lab_requests.*.specimen' => ['nullable', 'string', 'max:255'],
            'lab_requests.*.priority' => ['nullable', Rule::in(['routine', 'urgent'])],
            'lab_requests.*.instructions' => ['nullable', 'string', 'max:2000'],

            'scan_requests' => ['nullable', 'array', 'max:20'],
            'scan_requests.*.scan_type' => ['nullable', 'string', 'max:255'],
            'scan_requests.*.body_area' => ['nullable', 'string', 'max:255'],
            'scan_requests.*.contrast_required' => ['nullable', 'boolean'],
            'scan_requests.*.instructions' => ['nullable', 'string', 'max:2000'],

            'prescriptions' => ['nullable', 'array', 'max:30'],
            'prescriptions.*.medication_name' => ['nullable', 'string', 'max:255'],
            'prescriptions.*.dosage' => ['nullable', 'string', 'max:255'],
            'prescriptions.*.frequency' => ['nullable', 'string', 'max:255'],
            'prescriptions.*.duration' => ['nullable', 'string', 'max:255'],
            'prescriptions.*.quantity' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'prescriptions.*.instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => trim((string) $this->input('title')),
            'status' => $this->input('status', Diagnosis::STATUS_ACTIVE),
            'diagnosed_at' => $this->input('diagnosed_at') ?: now()->toDateTimeString(),
        ]);
    }
}

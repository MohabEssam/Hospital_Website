<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AppointmentConflictService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
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
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', Rule::in(Appointment::statusOptions())],
            'treatment' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'fee' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $startTime = (string) $this->input('start_time');
        $endTime = $this->input('end_time');

        if ($startTime !== '' && blank($endTime)) {
            $endTime = Carbon::createFromFormat('H:i', $startTime)
                ->addMinutes(30)
                ->format('H:i');
        }

        $this->merge([
            'treatment' => trim((string) $this->input('treatment')),
            'status' => $this->input('status', Appointment::STATUS_PENDING),
            'end_time' => $endTime,
            'fee' => $this->input('fee', 0),
        ]);
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $doctor = Doctor::find($this->integer('doctor_id'));

                if (! $doctor) {
                    return;
                }

                if (
                    ! $doctor->isAvailable()
                    && $this->input('status') !== Appointment::STATUS_CANCELLED
                ) {
                    $validator->errors()->add('doctor_id', 'The selected doctor is currently unavailable.');
                }

                $hasConflict = app(AppointmentConflictService::class)->hasConflict(
                    $doctor->getKey(),
                    (string) $this->input('appointment_date'),
                    (string) $this->input('start_time'),
                    (string) $this->input('end_time'),
                );

                if ($hasConflict) {
                    $validator->errors()->add('start_time', 'This appointment overlaps with another booking for the selected doctor.');
                }
            },
        ];
    }
}

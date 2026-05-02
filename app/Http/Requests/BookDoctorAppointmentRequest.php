<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AppointmentConflictService;
use App\Services\DoctorScheduleService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookDoctorAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPatient() === true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'treatment' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'min:7', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'treatment' => trim((string) $this->input('treatment', 'Consultation')),
            'notes' => trim((string) $this->input('notes', '')),
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

                /** @var Doctor|null $doctor */
                $doctor = $this->route('doctor');

                if (! $doctor || ! $doctor->isAvailable()) {
                    $validator->errors()->add('doctor', 'The selected doctor is currently unavailable.');

                    return;
                }

                if (! app(DoctorScheduleService::class)->slotIsAvailable(
                    $doctor,
                    (string) $this->input('appointment_date'),
                    (string) $this->input('start_time'),
                )) {
                    $validator->errors()->add('start_time', 'This time slot is not available in the doctor schedule.');

                    return;
                }

                $startTime = Carbon::createFromFormat('H:i', (string) $this->input('start_time'));
                $endTime = $startTime->copy()->addMinutes(30)->format('H:i');

                if (app(AppointmentConflictService::class)->hasConflict(
                    $doctor->getKey(),
                    (string) $this->input('appointment_date'),
                    $startTime->format('H:i'),
                    $endTime,
                )) {
                    $validator->errors()->add('start_time', 'This appointment slot has just been booked. Please choose another time.');
                }
            },
        ];
    }
}

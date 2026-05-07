<?php

namespace App\Http\Requests;

use App\Models\ServiceBooking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookServiceRequest extends FormRequest
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
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required', 'date_format:H:i'],
            'phone_number' => ['required', 'string', 'min:7', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
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

                $service = $this->route('service');
                $patient = $this->user()->patientProfile;

                if (! $service || ! $service->is_bookable) {
                    $validator->errors()->add('service', 'This service does not accept bookings.');
                    return;
                }

                if (! $patient) {
                    $validator->errors()->add('patient', 'Patient profile not found.');
                    return;
                }

                $exists = ServiceBooking::query()
                    ->where('patient_id', $patient->getKey())
                    ->where('patient_care_service_id', $service->getKey())
                    ->where('booking_date', $this->input('booking_date'))
                    ->where('booking_time', $this->input('booking_time'))
                    ->whereIn('status', [ServiceBooking::STATUS_PENDING, ServiceBooking::STATUS_CONFIRMED])
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('booking_date', 'You already have a booking for this service at the selected date and time.');
                }
            },
        ];
    }
}

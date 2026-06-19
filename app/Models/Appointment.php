<?php

namespace App\Models;

use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'department_id',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'treatment',
        'notes',
        'fee',
        'phone_number',
        'confirmation_email_sent_at',
        'auto_confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'fee' => 'decimal:2',
            'confirmation_email_sent_at' => 'datetime',
            'auto_confirmed_at' => 'datetime',
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_CONFIRMED,
            self::STATUS_CANCELLED,
            self::STATUS_COMPLETED,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function slotBlockingStatuses(): array
    {
        return [
            self::STATUS_CONFIRMED,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function blocksSlot(): bool
    {
        return in_array($this->status, self::slotBlockingStatuses(), true);
    }
}

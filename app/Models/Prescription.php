<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    use HasPublicId;

    public const STATUS_PENDING = 'pending';

    public const STATUS_DISPENSED = 'dispensed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'public_id',
        'patient_id',
        'doctor_id',
        'diagnosis_id',
        'dispensed_by_id',
        'medication_name',
        'dosage',
        'frequency',
        'duration',
        'quantity',
        'instructions',
        'status',
        'prescribed_at',
        'dispensed_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'prescribed_at' => 'datetime',
            'dispensed_at' => 'datetime',
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

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensed_by_id');
    }

    public function isDispensed(): bool
    {
        return $this->status === self::STATUS_DISPENSED;
    }

    protected static function publicIdPrefix(): string
    {
        return 'RX';
    }
}

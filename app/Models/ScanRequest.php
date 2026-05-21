<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScanRequest extends Model
{
    use HasPublicId;

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'public_id',
        'patient_id',
        'doctor_id',
        'diagnosis_id',
        'scan_type',
        'body_area',
        'contrast_required',
        'instructions',
        'status',
        'requested_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'contrast_required' => 'boolean',
            'requested_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function result(): HasOne
    {
        return $this->hasOne(ScanResult::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    protected static function publicIdPrefix(): string
    {
        return 'SCANREQ';
    }
}

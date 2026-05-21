<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanResult extends Model
{
    use HasPublicId;

    protected $fillable = [
        'public_id',
        'scan_request_id',
        'patient_id',
        'doctor_id',
        'entered_by_id',
        'findings',
        'impression',
        'image_paths',
        'status',
        'resulted_at',
    ];

    protected function casts(): array
    {
        return [
            'image_paths' => 'array',
            'resulted_at' => 'datetime',
        ];
    }

    public function scanRequest(): BelongsTo
    {
        return $this->belongsTo(ScanRequest::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by_id');
    }

    protected static function publicIdPrefix(): string
    {
        return 'SCANRES';
    }
}

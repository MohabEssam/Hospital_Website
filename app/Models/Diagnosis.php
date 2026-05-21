<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diagnosis extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'title',
        'summary',
        'symptoms',
        'notes',
        'status',
        'diagnosed_at',
    ];

    protected function casts(): array
    {
        return [
            'diagnosed_at' => 'datetime',
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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function labRequests(): HasMany
    {
        return $this->hasMany(LabRequest::class);
    }

    public function scanRequests(): HasMany
    {
        return $this->hasMany(ScanRequest::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}

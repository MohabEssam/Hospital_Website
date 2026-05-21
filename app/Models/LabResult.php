<?php

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    use HasPublicId;

    protected $fillable = [
        'public_id',
        'lab_request_id',
        'patient_id',
        'doctor_id',
        'lab_id',
        'entered_by_id',
        'result_text',
        'file_paths',
        'status',
        'resulted_at',
    ];

    protected function casts(): array
    {
        return [
            'file_paths' => 'array',
            'resulted_at' => 'datetime',
        ];
    }

    public function labRequest(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by_id');
    }

    protected static function publicIdPrefix(): string
    {
        return 'LABRES';
    }
}

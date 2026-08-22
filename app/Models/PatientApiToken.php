<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PatientApiToken extends Model
{
    protected $fillable = ['patient_id', 'token', 'name', 'last_used_at', 'expires_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public static function generateForPatient(int $patientId, string $name = 'mobile'): self
    {
        return static::create([
            'patient_id' => $patientId,
            'token' => hash('sha256', Str::random(60) . microtime()),
            'name' => $name,
            'expires_at' => now()->addYear(),
        ]);
    }

    public function patient() { return $this->belongsTo(Patient::class); }

    public function isValid(): bool
    {
        return !$this->expires_at || $this->expires_at->isFuture();
    }
}

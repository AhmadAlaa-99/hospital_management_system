<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = [
        'diagnostic_id', 'medicine_name', 'dosage', 'frequency', 'duration_days', 'instructions',
    ];

    public function diagnostic()
    {
        return $this->belongsTo(Diagnostic::class);
    }
}

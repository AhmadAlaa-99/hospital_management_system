<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalRecord extends Model
{
    protected $fillable = ['patient_id', 'title', 'type', 'file_path', 'notes'];

    public static $typeLabels = [
        'lab' => 'تحليل',
        'ray' => 'أشعة',
        'report' => 'تقرير',
        'prescription' => 'وصفة',
        'other' => 'أخرى',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
}

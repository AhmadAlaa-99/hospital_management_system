<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbulanceRequest extends Model
{
    protected $fillable = [
        'patient_name', 'phone', 'address', 'notes', 'ambulance_id', 'status', 'requested_at',
    ];

    protected $casts = ['requested_at' => 'datetime'];

    public static $statusLabels = [
        'pending' => 'قيد الانتظار',
        'dispatched' => 'تم الإرسال',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغى',
    ];

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

    /** سيارات مرتبطة بطلبات قيد التنفيذ (لم تُكتمل بعد) */
    public static function busyAmbulanceIds(): array
    {
        return static::query()
            ->where('status', 'dispatched')
            ->whereNotNull('ambulance_id')
            ->pluck('ambulance_id')
            ->all();
    }
}

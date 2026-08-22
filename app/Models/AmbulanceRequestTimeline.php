<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmbulanceRequestTimeline extends Model
{
    protected $fillable = ['ambulance_request_id', 'status', 'notes', 'recorded_at'];

    protected $casts = ['recorded_at' => 'datetime'];

    public static $statusLabels = [
        'pending' => 'تم الاستلام',
        'dispatched' => 'تم الإرسال',
        'en_route' => 'في الطريق',
        'arrived' => 'وصلت السيارة',
        'transported' => 'تم نقل المريض',
        'completed' => 'اكتمل',
        'cancelled' => 'ملغى',
    ];

    public function ambulanceRequest() { return $this->belongsTo(AmbulanceRequest::class); }
}

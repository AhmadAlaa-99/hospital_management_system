<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'message',
        'action_url',
        'reader_status',
    ];

    public function scopeCountNotification($query, $user_id)
    {
        $query->where('user_id', $user_id)->where('reader_status', 0);
    }

    public function scopeForAuthUser($query)
    {
        $userType = \App\Services\NotificationService::currentUserType();

        return $query->where('user_id', auth()->id())
            ->where(function ($q) use ($userType) {
                $q->where('user_type', $userType)->orWhereNull('user_type');
            });
    }
}

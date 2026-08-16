<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function scopechekConversation($query, $auth_email, $receiver_email)
    {
        return $query->where(function ($q) use ($auth_email, $receiver_email) {
            $q->where('sender_email', $auth_email)
                ->where('receiver_email', $receiver_email);
        })->orWhere(function ($q) use ($auth_email, $receiver_email) {
            $q->where('sender_email', $receiver_email)
                ->where('receiver_email', $auth_email);
        });
    }

     public function messages(){

        return $this->hasMany(Message::class);
     }
}

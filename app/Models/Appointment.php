<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    //use Translatable;
    use HasFactory;
    //public $translatedAttributes = ['name'];
    public $fillable= ['name','email','phone','notes','doctor_id','section_id','type','appointment','preferred_date','preferred_time','reminder_sent','consultation_type','meeting_url','is_emergency'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class,'section_id');
    }

    public function queueTickets()
    {
        return $this->hasMany(QueueTicket::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}

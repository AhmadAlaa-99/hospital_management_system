<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    use Translatable;
    use HasFactory;
    public $translatedAttributes = ['name','Address'];
    public $fillable= ['email','password','Password','Date_Birth','Phone','Gender','Blood_Group','insurance_id'];

    public function getAuthPassword()
    {
        return $this->attributes['password'] ?? $this->attributes['Password'] ?? null;
    }

    public function insurance()
    {
        return $this->belongsTo(Insurance::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Invoice::class,'doctor_id');
    }

    public function service()
    {
        return $this->belongsTo(Invoice::class,'Service_id');
    }
}

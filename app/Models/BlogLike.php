<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogLike extends Model
{
    protected $fillable = ['blog_id', 'patient_id'];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

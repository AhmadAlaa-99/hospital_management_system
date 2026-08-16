<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogComment extends Model
{
    protected $fillable = ['blog_id', 'patient_id', 'body'];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

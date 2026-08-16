<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use Translatable;
    use HasFactory;

    protected $table = 'Services';

    public $translationForeignKey = 'Service_id';

    public $translatedAttributes = ['name'];
    public $fillable= ['price','description','status'];
}

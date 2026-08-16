<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'excerpt',
        'body',
        'author',
        'views',
        'likes',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function imageUrl(): string
    {
        $path = ($this->image && file_exists(public_path($this->image)))
            ? $this->image
            : 'WebSite/images/hms/blogs/heart.jpg';

        // مسار نسبي يشتغل مع artisan serve و Laragon
        return '/' . ltrim(str_replace('\\', '/', $path), '/');
    }

    public static function makeSlug(string $title): string
    {
        $slug = Str::slug($title);
        if ($slug === '') {
            $slug = 'article-' . Str::random(6);
        }

        $original = $slug;
        $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->latest();
    }

    public function likesRelation()
    {
        return $this->hasMany(BlogLike::class);
    }

    public function isLikedByPatient(?int $patientId): bool
    {
        if (!$patientId) {
            return false;
        }

        return $this->likesRelation()->where('patient_id', $patientId)->exists();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_name',
        'address',
        'city',
        'phone',
        'phone2',
        'email',
        'working_hours',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'whatsapp',
        'about',
        'copyright',
    ];

    public static function current(): self
    {
        return static::query()->first() ?: static::create([
            'hospital_name' => 'مستشفى الشام التخصصي للعيادات الشاملة التخصصية',
            'address' => 'شارع أبو رمانة، مقابل حديقة الجلاء',
            'city' => 'دمشق، سوريا',
            'phone' => '+963 11 334 2200',
            'phone2' => '+963 933 456 789',
            'email' => 'info@alsham-hospital.sy',
            'working_hours' => 'السبت - الخميس: 8:00 ص - 8:00 م',
            'facebook' => 'https://facebook.com',
            'twitter' => 'https://twitter.com',
            'instagram' => 'https://instagram.com',
            'linkedin' => 'https://linkedin.com',
            'whatsapp' => '+963933456789',
            'about' => 'مستشفى عيادات تخصصية خاصة يضم عيادات شاملة تخصصية وقسم إسعاف للحالات البسيطة — لا يُعد مستشفى عاماً شاملاً للحالات الحرجة أو الجراحية الكبرى.',
            'copyright' => 'مستشفى الشام التخصصي للعيادات الشاملة التخصصية © جميع الحقوق محفوظة',
        ]);
    }
}

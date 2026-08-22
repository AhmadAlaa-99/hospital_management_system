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
        'sham_cash_enabled',
        'sham_cash_wallet',
        'sham_cash_qr_path',
        'sham_cash_instructions',
    ];

    protected $casts = [
        'sham_cash_enabled' => 'boolean',
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
            'about' => 'مستشفى عيادات تخصصية.',
            'copyright' => 'مستشفى الشام التخصصي للعيادات الشاملة التخصصية © جميع الحقوق محفوظة',
            'sham_cash_enabled' => true,
            'sham_cash_wallet' => '0999123456',
            'sham_cash_instructions' => "1. افتح تطبيق شام كاش\n2. ادفع المبلغ الظاهر في الفاتورة\n3. ارفع screenshot الإيصال في النظام\n4. انتظر مراجعة الإدارة",
        ]);
    }
}

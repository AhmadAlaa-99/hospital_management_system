<?php

namespace App\Helpers;

class FriendlyError
{
    public static function message(string $raw): string
    {
        if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'doctors_email')) {
            return 'البريد الإلكتروني مستخدم مسبقاً لطبيب آخر.';
        }

        if (str_contains($raw, 'Duplicate entry') && str_contains($raw, 'doctors_phone')) {
            return 'رقم الهاتف مستخدم مسبقاً لطبيب آخر.';
        }

        if (str_contains($raw, 'Duplicate entry')) {
            return 'هذه البيانات مستخدمة مسبقاً — يرجى التحقق والمحاولة مجدداً.';
        }

        if (str_contains($raw, 'Permission denied') || str_contains($raw, 'fopen')) {
            return 'تعذر حفظ الملف. تحقق من صلاحيات مجلد الصور على الخادم (public/Dashboard/img/doctors).';
        }

        if (str_contains($raw, 'SQLSTATE') || str_contains($raw, 'Integrity constraint')) {
            return 'حدث خطأ أثناء حفظ البيانات — يرجى التحقق من المدخلات والمحاولة مجدداً.';
        }

        return $raw;
    }
}

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $certificate->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
        .ref { color: #666; font-size: 12px; }
        .content { line-height: 1.8; margin: 30px 0; }
        .footer { margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $certificate->title }}</h2>
        <p class="ref">رقم المرجع: {{ $certificate->reference_number }}</p>
        <p>{{ \App\Models\MedicalCertificate::$typeLabels[$certificate->type] ?? '' }}</p>
    </div>
    <p><strong>المريض:</strong> {{ optional($certificate->patient)->name ?? '—' }}</p>
    <p><strong>الطبيب:</strong> {{ optional($certificate->doctor)->name ?? '—' }}</p>
    <p><strong>تاريخ الإصدار:</strong> {{ $certificate->issued_at->format('Y-m-d') }}</p>
    @if($certificate->days_off)
        <p><strong>مدة الإجازة:</strong> {{ $certificate->days_off }} يوم</p>
    @endif
    <div class="content">{!! nl2br(e($certificate->content)) !!}</div>
    <div class="footer">
        <p>توقيع الطبيب: ____________________</p>
        <p>ختم المركز: ____________________</p>
    </div>
</body>
</html>

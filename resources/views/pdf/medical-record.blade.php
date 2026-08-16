<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><title>السجل الطبي</title>
<style>
body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#333}
h1,h2{color:#1a5276;border-bottom:1px solid #ddd;padding-bottom:5px}
table{width:100%;border-collapse:collapse;margin:10px 0}
th,td{border:1px solid #ccc;padding:6px;text-align:right}
th{background:#ecf0f1}
.section{margin-bottom:20px}
</style></head>
<body>
<h1>السجل الطبي — {{ $patient->name }}</h1>
<p>البريد: {{ $patient->email }} | الهاتف: {{ $patient->Phone }} | فصيلة الدم: {{ $patient->Blood_Group }}</p>

<div class="section"><h2>التشخيصات والوصفات</h2>
@if($diagnostics->isEmpty())<p>لا توجد تشخيصات</p>@endif
@foreach($diagnostics as $d)
    <p><strong>{{ $d->date }}</strong> — {{ optional($d->Doctor)->name }}</p>
    <p>{{ $d->diagnosis }}</p>
    @if($d->prescriptions->count())
        <table><tr><th>الدواء</th><th>الجرعة</th><th>التكرار</th><th>المدة</th></tr>
        @foreach($d->prescriptions as $rx)
            <tr><td>{{ $rx->medicine_name }}</td><td>{{ $rx->dosage }}</td><td>{{ $rx->frequency }}</td><td>{{ $rx->duration_days }} يوم</td></tr>
        @endforeach</table>
    @elseif($d->medicine)<p><em>الأدوية:</em> {{ $d->medicine }}</p>@endif
@endforeach
</div>

<div class="section"><h2>الأشعة</h2>
@forelse($rays as $r)<p>{{ $r->date ?? '' }} — {{ $r->description }}</p>@empty<p>لا توجد</p>@endforelse
</div>

<div class="section"><h2>المختبر</h2>
@forelse($labs as $l)<p>{{ $l->date ?? '' }} — {{ $l->description }}</p>@empty<p>لا توجد</p>@endforelse
</div>

<p style="margin-top:30px;font-size:10px;color:#888">تم التصدير {{ now()->format('Y-m-d H:i') }}</p>
</body></html>

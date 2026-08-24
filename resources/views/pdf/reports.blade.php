<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تقرير النظام</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; font-size: 11px; padding: 16px; color: #222; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 13px; margin: 18px 0 8px; border-bottom: 1px solid #999; padding-bottom: 4px; }
        .meta { text-align: center; color: #666; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 5px 6px; text-align: center; }
        th { background: #f0f0f0; }
        .stats-grid { width: 100%; margin-bottom: 8px; }
        .stats-grid td { border: none; padding: 3px 8px; text-align: right; }
        .stats-grid td.value { font-weight: bold; text-align: left; }
    </style>
</head>
<body>
    <h1>التقارير والإحصائيات</h1>
    <p class="meta">مستشفى الشام التخصصي — {{ date('Y-m-d H:i') }}</p>

    <h2>ملخص الإحصائيات</h2>
    <table class="stats-grid">
        <tr><td>إجمالي المرضى</td><td class="value">{{ $stats['total_patients'] }}</td></tr>
        <tr><td>إجمالي الإيرادات</td><td class="value">{{ number_format($stats['total_revenue'], 0) }}</td></tr>
        <tr><td>مواعيد مؤكدة</td><td class="value">{{ $stats['confirmed_appointments'] }}</td></tr>
        <tr><td>متوسط تقييم الأطباء</td><td class="value">{{ $stats['avg_doctor_rating'] }} / 5</td></tr>
        <tr><td>مواعيد مرفوضة (No-Show)</td><td class="value">{{ $stats['no_show_count'] }}</td></tr>
        <tr><td>استشارات عن بُعد</td><td class="value">{{ $stats['telemedicine_count'] }}</td></tr>
        <tr><td>مواعيد طوارئ/إسعاف</td><td class="value">{{ $stats['emergency_appointments'] }}</td></tr>
        <tr><td>مطالبات تأمين معلقة</td><td class="value">{{ $stats['pending_claims'] }}</td></tr>
    </table>

    <h2>المرضى والإيرادات الشهرية (6 أشهر)</h2>
    <table>
        <thead><tr><th>الشهر</th><th>مرضى جدد</th><th>الإيرادات</th></tr></thead>
        <tbody>
        @foreach($months as $month)
            <tr>
                <td>{{ $month }}</td>
                <td>{{ $patientsByMonth[$month] ?? 0 }}</td>
                <td>{{ number_format($revenueByMonth[$month] ?? 0, 0) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>أداء الأقسام</h2>
    <table>
        <thead><tr><th>القسم</th><th>الأطباء</th><th>المواعيد</th><th>الفواتير</th><th>الإيرادات</th></tr></thead>
        <tbody>
        @foreach($sectionPerformance as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['doctors'] }}</td>
                <td>{{ $row['appointments'] }}</td>
                <td>{{ $row['invoices'] }}</td>
                <td>{{ number_format($row['revenue'], 0) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>أكثر التشخيصات</h2>
    <table>
        <thead><tr><th>التشخيص</th><th>العدد</th></tr></thead>
        <tbody>
        @foreach($topDiagnoses as $d)
            <tr><td>{{ $d->diagnosis }}</td><td>{{ $d->total }}</td></tr>
        @endforeach
        </tbody>
    </table>

    <h2>متوسط انتظار العيادات (دقيقة)</h2>
    <table>
        <thead><tr><th>القسم</th><th>متوسط الانتظار</th></tr></thead>
        <tbody>
        @foreach($sectionWaitStats as $s)
            <tr><td>{{ $s['name'] }}</td><td>{{ $s['avg_wait_minutes'] }}</td></tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>

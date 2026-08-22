<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ $pharmacyInvoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; direction: rtl; font-size: 12px; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background: #f0f0f0; }
        .total { font-size: 14px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>صيدلية العيادة</h2>
        <p>فاتورة صيدلية — {{ $pharmacyInvoice->invoice_number }}</p>
    </div>
    <p><strong>المريض:</strong> {{ optional($pharmacyInvoice->patient)->name }}</p>
    <p><strong>الطبيب:</strong> {{ optional($pharmacyInvoice->doctor)->name ?? '—' }}</p>
    <p><strong>التاريخ:</strong> {{ $pharmacyInvoice->issued_at->format('Y-m-d H:i') }}</p>
    <table>
        <thead>
        <tr><th>الدواء</th><th>الوصفة</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr>
        </thead>
        <tbody>
        @foreach($pharmacyInvoice->items as $item)
            <tr>
                <td>{{ optional($item->medicine)->name }}</td>
                <td>{{ optional($item->prescription)->medicine_name ?? '—' }}</td>
                <td>{{ $item->quantity_dispensed }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p class="total">الإجمالي: {{ number_format($pharmacyInvoice->total_amount, 2) }}</p>
    <p style="margin-top:30px;font-size:10px;color:#666;">صيدلية العيادة — كشف → وصفة → صرف</p>
</body>
</html>

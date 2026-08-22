@extends('Dashboard.layouts.master')
@section('title') فاتورة صيدلية @endsection
@section('content')
<div class="card hms-table-card">
    <div class="card-header">فاتورة {{ $pharmacyInvoice->invoice_number }}</div>
    <div class="card-body">
        <p><strong>الطبيب:</strong> {{ optional($pharmacyInvoice->doctor)->name ?? '—' }}</p>
        <p><strong>التاريخ:</strong> {{ $pharmacyInvoice->issued_at->format('Y-m-d') }}</p>
        <table class="table hms-table">
            <thead><tr><th>الدواء</th><th>الكمية</th><th>السعر</th></tr></thead>
            <tbody>
            @foreach($pharmacyInvoice->items as $item)
                <tr>
                    <td>{{ optional($item->medicine)->name }}</td>
                    <td>{{ $item->quantity_dispensed }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><th colspan="2">الإجمالي</th><th>{{ number_format($pharmacyInvoice->total_amount, 2) }}</th></tr></tfoot>
        </table>
        <a href="{{ route('patient.pharmacy.index') }}" class="btn btn-secondary mt-2">رجوع</a>
    </div>
</div>
@endsection

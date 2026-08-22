@extends('Dashboard.layouts.master')
@section('title') فاتورة صيدلية {{ $pharmacyInvoice->invoice_number }} @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>فاتورة صيدلية — {{ $pharmacyInvoice->invoice_number }}</span>
        <div>
            <a href="{{ route('pharmacy.invoices.pdf', $pharmacyInvoice) }}" class="btn btn-sm btn-success">تحميل PDF</a>
            <a href="{{ route('pharmacy.invoices') }}" class="btn btn-sm btn-secondary">رجوع</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>المريض:</strong> {{ optional($pharmacyInvoice->patient)->name }}</p>
                <p><strong>الطبيب:</strong> {{ optional($pharmacyInvoice->doctor)->name ?? '—' }}</p>
            </div>
            <div class="col-md-6 text-left">
                <p><strong>التاريخ:</strong> {{ $pharmacyInvoice->issued_at->format('Y-m-d H:i') }}</p>
                @if($pharmacyInvoice->diagnostic)
                    <p><strong>مرتبطة بتشخيص:</strong> #{{ $pharmacyInvoice->diagnostic_id }}</p>
                @endif
            </div>
        </div>
        <table class="table hms-table">
            <thead><tr><th>الدواء</th><th>من الوصفة</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr></thead>
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
            <tfoot>
            <tr><th colspan="4" class="text-left">الإجمالي</th><th>{{ number_format($pharmacyInvoice->total_amount, 2) }}</th></tr>
            </tfoot>
        </table>
        @if($pharmacyInvoice->notes)
            <p class="text-muted mt-2"><small>{{ $pharmacyInvoice->notes }}</small></p>
        @endif
    </div>
</div>
@endsection

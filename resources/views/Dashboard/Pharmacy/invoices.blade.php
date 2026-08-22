@extends('Dashboard.layouts.master')
@section('title') فواتير الصيدلية @endsection
@section('content')
<div class="card hms-table-card">
    <div class="card-header d-flex justify-content-between">
        <span>فواتير صيدلية العيادة</span>
        <a href="{{ route('pharmacy.index') }}" class="btn btn-sm btn-primary">الصيدلية</a>
    </div>
    <div class="card-body">
        <table class="table hms-table">
            <thead><tr><th>رقم الفاتورة</th><th>المريض</th><th>الطبيب</th><th>المبلغ</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
            @forelse($invoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ optional($inv->patient)->name ?? '#'.$inv->patient_id }}</td>
                    <td>{{ optional($inv->doctor)->name ?? '—' }}</td>
                    <td>{{ number_format($inv->total_amount, 2) }}</td>
                    <td>{{ $inv->issued_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('pharmacy.invoices.show', $inv) }}" class="btn btn-sm btn-info">عرض</a>
                        <a href="{{ route('pharmacy.invoices.pdf', $inv) }}" class="btn btn-sm btn-success">PDF</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted text-center">لا توجد فواتير</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</div>
@endsection

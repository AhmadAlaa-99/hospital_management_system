@extends('Dashboard.layouts.master')
@section('title') فواتير الصيدلية @endsection
@section('content')
<div class="card hms-table-card">
    <div class="card-body">
        <table class="table hms-table">
            <thead><tr><th>رقم الفاتورة</th><th>الطبيب</th><th>المبلغ</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
            @forelse($invoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_number }}</td>
                    <td>{{ optional($inv->doctor)->name ?? '—' }}</td>
                    <td>{{ number_format($inv->total_amount, 2) }}</td>
                    <td>{{ $inv->issued_at->format('Y-m-d') }}</td>
                    <td><a href="{{ route('patient.pharmacy.show', $inv) }}" class="btn btn-sm btn-primary">التفاصيل</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">لا توجد فواتير صيدلية</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</div>
@endsection

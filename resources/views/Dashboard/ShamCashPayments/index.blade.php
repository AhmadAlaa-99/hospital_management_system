@extends('Dashboard.layouts.master')
@section('title') مدفوعات شام كاش @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card mb-3">
    <div class="card-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <h5 class="mb-0">مدفوعات شام كاش</h5>
        <div>
            <a href="{{ route('sham-cash-payments.index', ['status' => 'pending_review']) }}" class="btn btn-sm btn-warning">
                جاري المراجعة ({{ $counts['pending_review'] }})
            </a>
            <a href="{{ route('sham-cash-payments.index', ['status' => 'approved']) }}" class="btn btn-sm btn-success">معتمدة ({{ $counts['approved'] }})</a>
            <a href="{{ route('sham-cash-payments.index', ['status' => 'rejected']) }}" class="btn btn-sm btn-danger">مرفوضة ({{ $counts['rejected'] }})</a>
            <a href="{{ route('sham-cash-payments.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-secondary">الكل</a>
        </div>
    </div>
</div>
<div class="card hms-table-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table hms-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>رقم السجل</th>
                    <th>الفاتورة</th>
                    <th>المريض</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($payments as $payment)
                    <tr class="{{ $payment->status === 'pending_review' ? 'table-warning' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $payment->id }}</td>
                        <td>#{{ $payment->invoice_id }}</td>
                        <td>{{ optional($payment->patient)->name ?? '#'.$payment->patient_id }}</td>
                        <td>{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $payment->status === 'pending_review' ? 'warning' : ($payment->status === 'approved' ? 'success' : 'danger') }}">
                                {{ \App\Models\ShamCashPayment::$statusLabels[$payment->status] ?? $payment->status }}
                            </span>
                        </td>
                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('sham-cash-payments.show', $payment) }}" class="btn btn-sm btn-primary">مراجعة</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">لا توجد مدفوعات</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $payments->links() }}
    </div>
</div>
@endsection

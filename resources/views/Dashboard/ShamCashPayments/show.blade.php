@extends('Dashboard.layouts.master')
@section('title') مراجعة دفع شام كاش @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="row">
    <div class="col-lg-5">
        <div class="card hms-table-card mb-3">
            <div class="card-header">تفاصيل الفاتورة</div>
            <div class="card-body">
                <p><strong>فاتورة #{{ $shamCashPayment->invoice_id }}</strong></p>
                <p>المريض: {{ optional($shamCashPayment->patient)->name ?? '—' }}</p>
                <p>الطبيب: {{ optional(optional($shamCashPayment->invoice)->Doctor)->name ?? '—' }}</p>
                <p>الخدمة: {{ optional(optional($shamCashPayment->invoice)->Service)->name ?? optional(optional($shamCashPayment->invoice)->Group)->name ?? '—' }}</p>
                <p class="h5">المبلغ: {{ number_format($shamCashPayment->amount, 2) }}</p>
                @if($shamCashPayment->transaction_reference)
                    <p>مرجع العملية: <code>{{ $shamCashPayment->transaction_reference }}</code></p>
                @endif
                @if($shamCashPayment->patient_notes)
                    <p class="text-muted"><small>ملاحظات المريض: {{ $shamCashPayment->patient_notes }}</small></p>
                @endif
                <p>الحالة:
                    <span class="badge badge-warning">{{ \App\Models\ShamCashPayment::$statusLabels[$shamCashPayment->status] ?? $shamCashPayment->status }}</span>
                </p>
            </div>
        </div>
        @if($shamCashPayment->status === 'pending_review')
            <div class="card hms-table-card">
                <div class="card-body">
                    <form action="{{ route('sham-cash-payments.approve', $shamCashPayment) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="form-group">
                            <label>ملاحظات (اختياري)</label>
                            <textarea name="admin_notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('اعتماد الدفع وتسجيل سند القبض؟')">✓ اعتماد الدفع</button>
                    </form>
                    <form action="{{ route('sham-cash-payments.reject', $shamCashPayment) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>سبب الرفض</label>
                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="مثال: المبلغ غير مطابق"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('رفض الإيصال؟')">✗ رفض الإيصال</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
    <div class="col-lg-7">
        <div class="card hms-table-card">
            <div class="card-header d-flex justify-content-between">
                <span>إيصال الدفع</span>
                <a href="{{ route('sham-cash-payments.receipt', $shamCashPayment) }}" target="_blank" class="btn btn-sm btn-outline-primary">فتح بنافذة جديدة</a>
            </div>
            <div class="card-body text-center">
                @php $ext = pathinfo($shamCashPayment->receipt_path, PATHINFO_EXTENSION); @endphp
                @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                    <img src="{{ asset('storage/' . $shamCashPayment->receipt_path) }}" class="img-fluid" style="max-height:600px;border:1px solid #ddd;border-radius:8px;">
                @else
                    <iframe src="{{ asset('storage/' . $shamCashPayment->receipt_path) }}" style="width:100%;height:600px;border:1px solid #ddd;border-radius:8px;"></iframe>
                @endif
            </div>
        </div>
    </div>
</div>
<a href="{{ route('sham-cash-payments.index') }}" class="btn btn-secondary mt-3">رجوع</a>
@endsection

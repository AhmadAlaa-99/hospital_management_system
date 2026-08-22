@extends('Dashboard.layouts.master')
@section('title') دفع شام كاش @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="row">
    <div class="col-lg-5">
        <div class="card hms-table-card mb-3">
            <div class="card-header bg-success text-white">بيانات الدفع — شام كاش</div>
            <div class="card-body text-center">
                @if($settings->sham_cash_qr_path)
                    <img src="{{ asset('storage/' . $settings->sham_cash_qr_path) }}" alt="QR شام كاش" class="img-fluid mb-3" style="max-width:220px;border:1px solid #ddd;padding:8px;border-radius:8px;">
                @else
                    <div class="alert alert-warning">لم يُرفع QR بعد — استخدم عنوان المحفظة أدناه.</div>
                @endif
                <p class="mb-1"><strong>عنوان المحفظة:</strong></p>
                <p class="h5 text-primary user-select-all">{{ $settings->sham_cash_wallet }}</p>
                @if($settings->sham_cash_instructions)
                    <hr>
                    <p class="text-muted small text-right">{!! nl2br(e($settings->sham_cash_instructions)) !!}</p>
                @endif
            </div>
        </div>
        <div class="card hms-table-card">
            <div class="card-body">
                <p><strong>فاتورة #{{ $invoice->id }}</strong></p>
                <p>الطبيب: {{ optional($invoice->Doctor)->name ?? '—' }}</p>
                <p>الخدمة: {{ optional($invoice->Service)->name ?? optional($invoice->Group)->name ?? '—' }}</p>
                <p class="h4 text-success">المبلغ: {{ number_format($invoice->total_with_tax, 2) }}</p>
                <p>حالة الدفع:
                    <span class="badge badge-{{ $invoice->payment_status === 'paid' ? 'success' : ($invoice->payment_status === 'pending_review' ? 'warning' : 'secondary') }}">
                        {{ \App\Models\Invoice::$paymentStatusLabels[$invoice->payment_status] ?? $invoice->payment_status }}
                    </span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        @if($invoice->payment_status === 'pending_review')
            <div class="alert alert-info">
                تم رفع إيصال الدفع وهو <strong>جاري مراجعته</strong> من الإدارة.
                @if($latestPayment)
                    <br><small>تاريخ الإرسال: {{ $latestPayment->created_at->format('Y-m-d H:i') }}</small>
                @endif
            </div>
        @elseif($invoice->payment_status === 'paid')
            <div class="alert alert-success">تم اعتماد الدفع لهذه الفاتورة.</div>
        @elseif($invoice->canPayViaShamCash())
            <div class="card hms-table-card">
                <div class="card-header">رفع إيصال الدفع</div>
                <div class="card-body">
                    <ol class="mb-4">
                        <li>افتح تطبيق <strong>شام كاش</strong> على هاتفك</li>
                        <li>ادفع المبلغ <strong>{{ number_format($invoice->total_with_tax, 2) }}</strong> إلى المحفظة أعلاه</li>
                        <li>التقط screenshot للإيصال أو احفظه PDF</li>
                        <li>ارفع الإيصال في النموذج أدناه</li>
                    </ol>
                    <form action="{{ route('patient.sham-cash.store', $invoice) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>إيصال الدفع (صورة أو PDF) *</label>
                            <input type="file" name="receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="form-group">
                            <label>رقم العملية / المرجع (اختياري)</label>
                            <input type="text" name="transaction_reference" class="form-control" placeholder="مثال: SC-123456789">
                        </div>
                        <div class="form-group">
                            <label>ملاحظات (اختياري)</label>
                            <textarea name="patient_notes" class="form-control" rows="2" placeholder="أي ملاحظة للإدارة"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">إرسال للمراجعة</button>
                    </form>
                </div>
            </div>
        @endif
        <a href="{{ route('invoices.patient') }}" class="btn btn-secondary mt-2">رجوع للفواتير</a>
    </div>
</div>
@endsection

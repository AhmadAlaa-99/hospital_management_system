@extends('Dashboard.layouts.master')
@section('title') قائمة انتظار العيادة @endsection
@section('css')
    <link href="{{ URL::asset('Dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <h4 class="content-title mb-0">قائمة انتظار العيادة — د. {{ $doctor->name }}</h4>
    </div>
    <div>
        <a href="{{ route('queue.display.doctor', $doctor) }}" target="_blank" class="btn btn-info btn-sm">
            <i class="fas fa-tv"></i> شاشة العرض
        </a>
    </div>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')

@include('Dashboard.partials.help-box', [
    'title' => 'الكشف والموعد',
    'body' => '• <strong>بدء الكشف:</strong> كشف «كشف» تلقائي في قائمة الكشوفات.<br>
    • <strong>إضافة فاتورة:</strong> خدمة مفردة أو مجموعة خدمات من نافذة سريعة.<br>
    • <strong>إنهاء:</strong> إغلاق الزيارة وتحديث الموعد إلى «منتهي».'
])

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row row-sm mb-4">
    <div class="col-md-4">
        <div class="card text-center p-4 bg-primary text-white">
            <small>الرقم الحالي</small>
            <h1 class="display-3 mb-0" id="current-ticket">
                {{ optional($display['current'])->ticket_number ?? '—' }}
            </h1>
            <p class="mb-0" id="current-patient">
                {{ optional($display['current'])->patient_name ?? 'لا يوجد نداء حالياً' }}
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center p-4">
            <small>بالانتظار</small>
            <h2 class="mb-0">{{ $display['waiting_count'] }}</h2>
        </div>
    </div>
    <div class="col-md-4 d-flex align-items-center">
        <form action="{{ route('doctor.queue.call-next') }}" method="POST" class="w-100">
            @csrf
            <button type="submit" class="btn btn-success btn-lg btn-block">
                <i class="fas fa-bullhorn"></i> نداء التالي
            </button>
        </form>
    </div>
</div>

<div class="card hms-table-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table hms-table">
                <thead>
                <tr>
                    <th>الرقم</th>
                    <th>المريض</th>
                    <th>الأولوية</th>
                    <th>الحالة</th>
                    <th>العمليات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($tickets as $ticket)
                    <tr class="@if($ticket->status === 'called') table-warning @elseif($ticket->status === 'serving') table-info @endif">
                        <td><strong>{{ $ticket->ticket_number }}</strong></td>
                        <td>{{ $ticket->patient_name }}</td>
                        <td>{{ \App\Models\QueueTicket::$priorityLabels[$ticket->priority] ?? $ticket->priority }}</td>
                        <td>{{ \App\Models\QueueTicket::$statusLabels[$ticket->status] ?? $ticket->status }}</td>
                        <td>
                            @if($ticket->status === 'called')
                                <form action="{{ route('doctor.queue.recall', $ticket) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-warning">إعادة نداء</button></form>
                                <form action="{{ route('doctor.queue.serving', $ticket) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-info">بدء الكشف</button></form>
                                @if($ticket->patient_id)
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-open-doctor-invoice-modal"
                                            data-patient-id="{{ $ticket->patient_id }}"
                                            data-appointment-id="{{ $ticket->appointment_id }}">إضافة فاتورة</button>
                                @endif
                                <form action="{{ route('doctor.queue.no-show', $ticket) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-secondary">لم يحضر</button></form>
                            @elseif($ticket->status === 'serving')
                                @if($ticket->patient_id)
                                    <button type="button" class="btn btn-sm btn-primary btn-open-doctor-invoice-modal"
                                            data-patient-id="{{ $ticket->patient_id }}"
                                            data-appointment-id="{{ $ticket->appointment_id }}">إضافة فاتورة</button>
                                @endif
                                <form action="{{ route('doctor.queue.complete', $ticket) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">إنهاء</button></form>
                            @elseif($ticket->status === 'waiting')
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">لا يوجد مرضى في قائمة الانتظار</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('Dashboard.doctor.invoices.add_invoice_modal')
@endsection
@section('js')
<script src="{{ URL::asset('Dashboard/plugins/select2/js/select2.min.js') }}"></script>
@include('Dashboard.doctor.invoices.add_invoice_modal_scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
(function () {
    const sectionId = {{ $doctor->section_id }};
    const doctorId = {{ $doctor->id }};
    const pusherKey = @json(config('broadcasting.connections.pusher.key'));

    function applyPayload(payload) {
        const current = payload.current;
        document.getElementById('current-ticket').textContent = current ? current.ticket_number : '—';
        document.getElementById('current-patient').textContent = current ? current.patient_name : 'لا يوجد نداء حالياً';
    }

    if (pusherKey) {
        const pusher = new Pusher(pusherKey, { cluster: @json(config('broadcasting.connections.pusher.options.cluster')), encrypted: true });
        ['queue.section.' + sectionId, 'queue.doctor.' + doctorId].forEach(function (channelName) {
            pusher.subscribe(channelName).bind('queue-updated', function (data) {
                if (data.payload) applyPayload(data.payload);
            });
        });
    } else {
        setInterval(function () {
            fetch(@json(route('queue.data')) + '?section_id=' + sectionId + '&doctor_id=' + doctorId)
                .then(r => r.json()).then(applyPayload).catch(function () {});
        }, 8000);
    }
})();
</script>
@endsection

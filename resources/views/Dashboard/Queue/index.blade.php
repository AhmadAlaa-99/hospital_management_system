@extends('Dashboard.layouts.master')
@section('title') إدارة الانتظار @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <h4 class="content-title mb-0">إدارة الانتظار — الاستقبال</h4>
    </div>
    <div class="d-flex">
        @if($sectionId)
            <a href="{{ route('queue.display.section', $sectionId) }}" target="_blank" class="btn btn-info btn-sm ml-2">
                <i class="fas fa-tv"></i> شاشة العرض
            </a>
        @endif
        <a href="{{ route('queue.track') }}" target="_blank" class="btn btn-secondary btn-sm">
            <i class="fas fa-search"></i> تتبع الرقم
        </a>
    </div>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

@include('Dashboard.partials.help-box', [
    'title' => 'ربط الانتظار بالمواعيد',
    'body' => 'كل رقم انتظار يجب أن يكون مربوطاً ب<strong>موعد</strong>:<br>
    • <strong>من موعد مجدول:</strong> اختر مريضاً من مواعيد اليوم المؤكدة.<br>
    • <strong>مريض جديد:</strong> يُنشأ موعد مؤكد تلقائياً ثم يُصدر الرقم.'
])

<div class="row row-sm mb-3">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="form-inline flex-wrap">
                    <label class="mr-2">القسم:</label>
                    <select name="section_id" class="form-control mr-3 mb-2" onchange="this.form.submit()">
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ $sectionId == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                    <label class="mr-2">الطبيب:</label>
                    <select name="doctor_id" class="form-control mr-3 mb-2" onchange="this.form.submit()">
                        <option value="">— الكل —</option>
                        @foreach($doctors->where('section_id', $sectionId) as $doc)
                            <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row row-sm">
    <div class="col-lg-4">
        @if($appointmentsToday->count())
        <div class="card mb-3">
            <div class="card-header"><h5 class="mb-0">إصدار رقم — من موعد مجدول</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.queue.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $sectionId }}">
                    <div class="form-group">
                        <label>مواعيد اليوم المؤكدة (بدون رقم)</label>
                        <select name="appointment_id" class="form-control" required>
                            <option value="">— اختر الموعد —</option>
                            @foreach($appointmentsToday as $apt)
                                <option value="{{ $apt->id }}">
                                    {{ $apt->name }} — {{ optional($apt->doctor)->name }} — {{ $apt->appointment }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-ticket-alt ml-1"></i> تسجيل حضور وإصدار رقم
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="alert alert-info mb-3">لا توجد مواعيد مؤكدة لليوم بدون رقم انتظار في هذا القسم.</div>
        @endif

        <div class="card">
            <div class="card-header"><h5 class="mb-0">مريض جديد — إنشاء موعد + رقم</h5></div>
            <div class="card-body">
                <p class="text-muted small">للمريض الذي حضر بدون حجز: يُسجّل موعد مؤكد لليوم ثم يُصدر رقم الانتظار.</p>
                <form action="{{ route('admin.queue.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $sectionId }}">
                    <div class="form-group">
                        <label>اسم المريض</label>
                        <input type="text" name="patient_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>الهاتف</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>البريد (اختياري)</label>
                        <input type="email" name="email" class="form-control" placeholder="إن وُجد في سجل المرضى">
                    </div>
                    <div class="form-group">
                        <label>الطبيب <span class="text-danger">*</span></label>
                        <select name="doctor_id" class="form-control" required>
                            <option value="">— اختر الطبيب —</option>
                            @foreach($doctors->where('section_id', $sectionId) as $doc)
                                <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الأولوية</label>
                        <select name="priority" class="form-control">
                            @foreach(\App\Models\QueueTicket::$priorityLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">إنشاء موعد وإصدار رقم</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card hms-table-card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">قائمة الانتظار اليوم</h5>
                <span class="badge badge-primary">{{ $tickets->where('status', 'waiting')->count() }} بالانتظار</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table hms-table">
                        <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>المريض</th>
                            <th>الموعد</th>
                            <th>الطبيب</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                            <th>انتظار ~</th>
                            <th>العمليات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tickets as $ticket)
                            <tr class="@if($ticket->status === 'called') table-warning @elseif($ticket->status === 'serving') table-info @endif">
                                <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                <td>{{ $ticket->patient_name }}</td>
                                <td>
                                    @if($ticket->appointment)
                                        <small>{{ $ticket->appointment->appointment }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ optional($ticket->doctor)->name ?? '—' }}</td>
                                <td>{{ \App\Models\QueueTicket::$priorityLabels[$ticket->priority] ?? $ticket->priority }}</td>
                                <td>{{ \App\Models\QueueTicket::$statusLabels[$ticket->status] ?? $ticket->status }}</td>
                                <td>{{ $ticket->estimated_wait_minutes }} د</td>
                                <td>
                                    @if(in_array($ticket->status, ['waiting', 'called', 'serving']))
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#cancelQueue{{ $ticket->id }}">
                                            إلغاء
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">لا توجد أرقام اليوم</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @foreach($tickets as $ticket)
                    @if(in_array($ticket->status, ['waiting', 'called', 'serving']))
                        <div class="modal fade" id="cancelQueue{{ $ticket->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">إلغاء رقم الانتظار</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="mb-0">هل تريد إلغاء رقم <strong>{{ $ticket->ticket_number }}</strong> للمريض <strong>{{ $ticket->patient_name }}</strong>؟</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">تراجع</button>
                                        <form action="{{ route('admin.queue.cancel', $ticket) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

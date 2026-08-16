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
        <div class="card">
            <div class="card-header"><h5 class="mb-0">إصدار رقم جديد</h5></div>
            <div class="card-body">
                <form action="{{ route('admin.queue.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="section_id" value="{{ $sectionId }}">
                    @if($doctorId)<input type="hidden" name="doctor_id" value="{{ $doctorId }}">@endif
                    <div class="form-group">
                        <label>اسم المريض</label>
                        <input type="text" name="patient_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>الهاتف</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    @if(!$doctorId)
                    <div class="form-group">
                        <label>الطبيب (اختياري)</label>
                        <select name="doctor_id" class="form-control">
                            <option value="">— بدون تحديد —</option>
                            @foreach($doctors->where('section_id', $sectionId) as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="form-group">
                        <label>الأولوية</label>
                        <select name="priority" class="form-control">
                            @foreach(\App\Models\QueueTicket::$priorityLabels as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">إصدار رقم</button>
                </form>
            </div>
        </div>

        @if($appointmentsToday->count())
        <div class="card mt-3">
            <div class="card-header"><h5 class="mb-0">مواعيد اليوم — تسجيل حضور</h5></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($appointmentsToday as $apt)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $apt->name }}</strong><br>
                                <small>{{ optional($apt->doctor)->name }} — {{ $apt->appointment }}</small>
                            </div>
                            <form action="{{ route('admin.queue.check-in', $apt) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-success">حضور</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
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
                                <td>{{ optional($ticket->doctor)->name ?? '—' }}</td>
                                <td>{{ \App\Models\QueueTicket::$priorityLabels[$ticket->priority] ?? $ticket->priority }}</td>
                                <td>{{ \App\Models\QueueTicket::$statusLabels[$ticket->status] ?? $ticket->status }}</td>
                                <td>{{ $ticket->estimated_wait_minutes }} د</td>
                                <td>
                                    @if(in_array($ticket->status, ['waiting', 'called', 'serving']))
                                        <form action="{{ route('admin.queue.cancel', $ticket) }}" method="POST" class="d-inline" onsubmit="return confirm('إلغاء الرقم؟')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">إلغاء</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">لا توجد أرقام اليوم</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

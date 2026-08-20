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
    'title' => 'إضافة مريض للانتظار',
    'body' => 'اختر أحد المسارات الثلاثة — كل رقم انتظار مربوط ب<strong>موعد</strong>:<br>
    • <strong>موعد مجدول:</strong> مريض حجز لليوم ووصل — اختر موعده وأصدر الرقم.<br>
    • <strong>مريض مسجّل:</strong> له حساب في النظام — إن كان له موعد اليوم يُربط به، وإلا يُنشأ موعد مؤكد ثم الرقم.<br>
    • <strong>مريض جديد:</strong> غير موجود في النظام — يُنشأ حساب (كالتسجيل من الموقع) ثم موعد ورقم.'
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
        <div class="card">
            <div class="card-header pb-0 border-bottom-0">
                <ul class="nav nav-tabs card-header-tabs" id="queueReceptionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-scheduled" data-toggle="tab" href="#pane-scheduled" role="tab">موعد مجدول</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-existing" data-toggle="tab" href="#pane-existing" role="tab">مريض مسجّل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-new" data-toggle="tab" href="#pane-new" role="tab">مريض جديد</a>
                    </li>
                </ul>
            </div>
            <div class="card-body tab-content" id="queueReceptionContent">
                {{-- 1) موعد مجدول لليوم --}}
                <div class="tab-pane fade show active" id="pane-scheduled" role="tabpanel">
                    @if($appointmentsToday->count())
                        <p class="text-muted small mb-3">مريض له موعد مؤكد اليوم ولم يُصدر له رقم بعد.</p>
                        <form action="{{ route('admin.queue.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="flow" value="scheduled">
                            <input type="hidden" name="section_id" value="{{ $sectionId }}">
                            <div class="form-group">
                                <label>مواعيد اليوم المؤكدة (بدون رقم)</label>
                                <select name="appointment_id" class="form-control" required>
                                    <option value="">— اختر الموعد —</option>
                                    @foreach($appointmentsToday as $apt)
                                        <option value="{{ $apt->id }}" {{ old('appointment_id') == $apt->id ? 'selected' : '' }}>
                                            {{ $apt->name }} — {{ optional($apt->doctor)->name }} — {{ $apt->appointment }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-ticket-alt ml-1"></i> تسجيل حضور وإصدار رقم
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info mb-0">لا توجد مواعيد مؤكدة لليوم بدون رقم انتظار في هذا القسم.</div>
                    @endif
                </div>

                {{-- 2) مريض مسجّل في النظام --}}
                <div class="tab-pane fade" id="pane-existing" role="tabpanel">
                    <p class="text-muted small mb-3">اختر مريضاً من السجل. إن كان له موعد اليوم يُربط به، وإلا يُنشأ موعد مؤكد ثم يُصدر الرقم.</p>
                    <form action="{{ route('admin.queue.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="flow" value="existing">
                        <input type="hidden" name="section_id" value="{{ $sectionId }}">
                        <div class="form-group">
                            <label>المريض <span class="text-danger">*</span></label>
                            <select name="patient_id" class="form-control select2-patient" required>
                                <option value="">— اختر المريض —</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} — {{ $patient->Phone }} — {{ $patient->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>الطبيب <span class="text-danger">*</span></label>
                            <select name="doctor_id" class="form-control" required>
                                <option value="">— اختر الطبيب —</option>
                                @foreach($doctors->where('section_id', $sectionId) as $doc)
                                    <option value="{{ $doc->id }}" {{ old('doctor_id', $doctorId) == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>الأولوية</label>
                            <select name="priority" class="form-control">
                                @foreach(\App\Models\QueueTicket::$priorityLabels as $key => $label)
                                    <option value="{{ $key }}" {{ old('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">ربط الموعد وإصدار رقم</button>
                    </form>
                </div>

                {{-- 3) مريض جديد — حساب + موعد + رقم --}}
                <div class="tab-pane fade" id="pane-new" role="tabpanel">
                    <p class="text-muted small mb-3">مريض غير موجود في النظام — يُنشأ حساب (كالتسجيل من الموقع) ثم موعد مؤكد ورقم انتظار. كلمة المرور الافتراضية = رقم الهاتف.</p>
                    <form action="{{ route('admin.queue.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="flow" value="new">
                        <input type="hidden" name="section_id" value="{{ $sectionId }}">
                        <div class="form-group">
                            <label>اسم المريض <span class="text-danger">*</span></label>
                            <input type="text" name="patient_name" class="form-control" value="{{ old('patient_name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label>الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        <div class="form-group">
                            <label>الطبيب <span class="text-danger">*</span></label>
                            <select name="doctor_id" class="form-control" required>
                                <option value="">— اختر الطبيب —</option>
                                @foreach($doctors->where('section_id', $sectionId) as $doc)
                                    <option value="{{ $doc->id }}" {{ old('doctor_id', $doctorId) == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>الأولوية</label>
                            <select name="priority" class="form-control">
                                @foreach(\App\Models\QueueTicket::$priorityLabels as $key => $label)
                                    <option value="{{ $key }}" {{ old('priority') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-teal btn-block" style="background:#20c997;border-color:#20c997;color:#fff;">إنشاء حساب وموعد ورقم</button>
                    </form>
                </div>
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

@section('css')
    <link href="{{ URL::asset('Dashboard/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('js')
    <script src="{{ URL::asset('Dashboard/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(function () {
            $('.select2-patient').select2({
                width: '100%',
                placeholder: '— اختر المريض —',
                dir: 'rtl'
            });

            var flowTabMap = {
                scheduled: '#tab-scheduled',
                existing: '#tab-existing',
                new: '#tab-new'
            };
            var oldFlow = @json(old('flow'));
            if (oldFlow && flowTabMap[oldFlow]) {
                $(flowTabMap[oldFlow]).tab('show');
            }
        });
    </script>
@endsection

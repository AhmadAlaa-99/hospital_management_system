@extends('Dashboard.layouts.master')
@section('title') جدول الأطباء @endsection
@section('content')
@include('Dashboard.messages_alert')

<div class="card hms-form-card mb-3">
    <div class="card-header pb-0">
        <h5 class="mb-0 hms-form-title"><i class="fas fa-filter ml-1"></i> فلترة الجداول</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('doctor-schedules.index') }}" class="hms-form" id="hmsScheduleFilterForm">
            <div class="row">
                <div class="col-md-3 col-sm-6 form-group">
                    <label>القسم</label>
                    <select name="section_id" class="form-control">
                        <option value="">— كل الأقسام —</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ $filters['section_id'] == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <label>الطبيب</label>
                    <select name="doctor_id" class="form-control" id="hmsFilterDoctor">
                        <option value="">— كل الأطباء —</option>
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}" data-section="{{ $d->section_id }}" {{ $filters['doctor_id'] == $d->id ? 'selected' : '' }}>
                                {{ $d->name }} @if($d->section) ({{ $d->section->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6 form-group">
                    <label>اليوم</label>
                    <select name="day_of_week" class="form-control">
                        <option value="">— كل الأيام —</option>
                        @foreach(\App\Models\DoctorSchedule::$dayNames as $k => $v)
                            <option value="{{ $k }}" {{ (string) $filters['day_of_week'] === (string) $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 form-group">
                    <label>بحث باسم الطبيب</label>
                    <input type="text" name="q" class="form-control" value="{{ $filters['q'] }}" placeholder="اكتب اسم الطبيب...">
                </div>
                <div class="col-md-1 col-sm-6 form-group d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-hms-primary btn-block">فلترة</button>
                </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                <a href="{{ route('doctor-schedules.index') }}" class="btn btn-sm btn-secondary">إعادة ضبط</a>
                <span class="text-muted small">
                    عرض {{ $stats['filtered'] }} من {{ $stats['total'] }} سجل —
                    {{ $stats['doctors'] }} طبيب
                </span>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card hms-form-card hms-schedule-form-card">
            <div class="card-header pb-0">
                <h5 class="mb-0 hms-form-title">إضافة / تحديث جدول</h5>
                <small class="text-muted">اختر طبيباً ويوماً — إن وُجد جدول سابق يُحدَّث</small>
            </div>
            <div class="card-body">
                <form action="{{ route('doctor-schedules.store') }}" method="POST" id="hmsScheduleForm">@csrf
                    <div class="form-group">
                        <label>الطبيب</label>
                        <select name="doctor_id" id="hmsFormDoctor" class="form-control" required>
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}" data-section="{{ $d->section_id }}" {{ $filters['doctor_id'] == $d->id ? 'selected' : '' }}>
                                    {{ $d->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>اليوم</label>
                        <select name="day_of_week" id="hmsFormDay" class="form-control" required>
                            @foreach(\App\Models\DoctorSchedule::$dayNames as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label>من</label>
                            <input type="time" name="start_time" id="hmsFormStart" class="form-control" value="09:00" required>
                        </div>
                        <div class="col-6 form-group">
                            <label>إلى</label>
                            <input type="time" name="end_time" id="hmsFormEnd" class="form-control" value="17:00" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>مدة الموعد (دقيقة)</label>
                        <input type="number" name="slot_duration" id="hmsFormDuration" class="form-control" value="30" min="15" max="120" required>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary hms-schedule-preset" data-start="09:00" data-end="13:00">صباحي</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary hms-schedule-preset" data-start="14:00" data-end="17:00">مسائي</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary hms-schedule-preset" data-start="09:00" data-end="17:00">دوام كامل</button>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">حفظ الجدول</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card hms-table-card">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">جداول الأطباء</h5>
                <span class="badge badge-primary">{{ $scheduleRows->count() }} سجل</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table hms-table" id="hms-doctor-schedules-table">
                        <thead>
                        <tr>
                            <th>الطبيب</th>
                            <th>القسم</th>
                            <th>اليوم</th>
                            <th>من</th>
                            <th>إلى</th>
                            <th>المدة</th>
                            <th class="text-center">العمليات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($scheduleRows as $s)
                            <tr>
                                <td>{{ optional($s->doctor)->name ?? '—' }}</td>
                                <td>{{ optional(optional($s->doctor)->section)->name ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-light border">{{ \App\Models\DoctorSchedule::$dayNames[$s->day_of_week] ?? $s->day_of_week }}</span>
                                </td>
                                <td>{{ substr((string) $s->start_time, 0, 5) }}</td>
                                <td>{{ substr((string) $s->end_time, 0, 5) }}</td>
                                <td>{{ $s->slot_duration }} د</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary hms-schedule-edit"
                                            data-doctor="{{ $s->doctor_id }}"
                                            data-day="{{ $s->day_of_week }}"
                                            data-start="{{ substr((string) $s->start_time, 0, 5) }}"
                                            data-end="{{ substr((string) $s->end_time, 0, 5) }}"
                                            data-duration="{{ $s->slot_duration }}">
                                        تعديل
                                    </button>
                                    <form action="{{ route('doctor-schedules.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذا الجدول؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">لا توجد جداول مطابقة — غيّر الفلاتر أو أضف جدولاً جديداً</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
(function () {
    document.querySelectorAll('.hms-schedule-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('hmsFormStart').value = btn.dataset.start;
            document.getElementById('hmsFormEnd').value = btn.dataset.end;
        });
    });

    document.querySelectorAll('.hms-schedule-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('hmsFormDoctor').value = btn.dataset.doctor;
            document.getElementById('hmsFormDay').value = btn.dataset.day;
            document.getElementById('hmsFormStart').value = btn.dataset.start;
            document.getElementById('hmsFormEnd').value = btn.dataset.end;
            document.getElementById('hmsFormDuration').value = btn.dataset.duration;
            document.getElementById('hmsScheduleForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    var sectionFilter = document.querySelector('[name="section_id"]');
    var doctorFilter = document.getElementById('hmsFilterDoctor');
    if (sectionFilter && doctorFilter) {
        sectionFilter.addEventListener('change', function () {
            var sectionId = sectionFilter.value;
            Array.from(doctorFilter.options).forEach(function (opt, idx) {
                if (idx === 0) return;
                var match = !sectionId || opt.dataset.section === sectionId;
                opt.hidden = !match;
                opt.disabled = !match;
            });
            if (doctorFilter.selectedOptions[0] && doctorFilter.selectedOptions[0].disabled) {
                doctorFilter.value = '';
            }
        });
    }
})();
</script>
@endsection

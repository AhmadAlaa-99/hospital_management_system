@extends('Dashboard.layouts.master')
@section('title') باقات الفحص @endsection
@section('content')
@include('Dashboard.messages_alert')

<div class="card hms-table-card mb-3">
    <div class="card-header">تعيين مجموعة كباقة فحص</div>
    <div class="card-body">
        <form action="{{ route('health-packages.mark') }}" method="POST" class="form-row align-items-end">
            @csrf
            <div class="form-group col-md-4">
                <label>مجموعة الخدمات</label>
                <select name="group_id" class="form-control" required>
                    <option value="">— اختر المجموعة —</option>
                    @foreach($allGroups as $grp)
                        <option value="{{ $grp->id }}">{{ $grp->name }} ({{ $grp->service_group->count() }} خدمة)</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>نوع الباقة</label>
                <input type="text" name="package_type" class="form-control" placeholder="مثال: فحص شامل">
            </div>
            <div class="form-group col-md-2">
                <label>صلاحية (يوم)</label>
                <input type="number" name="validity_days" class="form-control" value="90" min="1" max="365">
            </div>
            <div class="form-group col-md-2">
                <label>باقة فحص؟</label>
                <select name="is_health_package" class="form-control" required>
                    <option value="1">نعم — باقة فحص</option>
                    <option value="0">لا — مجموعة عادية</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <button class="btn btn-primary btn-block">حفظ</button>
            </div>
        </form>
        <small class="text-muted">يمكن أيضاً تحديد «باقة فحص» عند إنشاء/تعديل مجموعة من صفحة مجموعة الخدمات.</small>
    </div>
</div>

<div class="card hms-table-card mb-3">
    <div class="card-header">تفعيل باقة لمريض</div>
    <div class="card-body">
        @if($packages->isEmpty())
            <div class="alert alert-warning mb-0">لا توجد باقات فحص — عيّن مجموعة كباقة من القسم أعلاه.</div>
        @else
            <form action="{{ route('health-packages.activate') }}" method="POST" class="form-row align-items-end">
                @csrf
                <div class="form-group col-md-5">
                    <label>المريض</label>
                    <select name="patient_id" class="form-control" required>
                        <option value="">— اختر المريض —</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }} — #{{ $patient->id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-5">
                    <label>الباقة</label>
                    <select name="group_id" class="form-control" required>
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}" {{ old('group_id') == $pkg->id ? 'selected' : '' }}>
                                {{ $pkg->name }} — {{ $pkg->validity_days ?? 90 }} يوم ({{ $pkg->service_group->count() }} خدمة)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <button class="btn btn-success btn-block">تفعيل</button>
                </div>
            </form>
        @endif
    </div>
</div>

<div class="card hms-table-card mb-3">
    <div class="card-header">باقات الفحص المعرفة</div>
    <div class="card-body">
        <table class="table hms-table">
            <thead><tr><th>الباقة</th><th>النوع</th><th>الصلاحية</th><th>الخدمات</th></tr></thead>
            <tbody>
            @forelse($packages as $pkg)
                <tr>
                    <td>{{ $pkg->name }}</td>
                    <td>{{ $pkg->package_type ?? '—' }}</td>
                    <td>{{ $pkg->validity_days ?? 90 }} يوم</td>
                    <td>{{ $pkg->service_group->count() }} خدمة</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted text-center">لا توجد باقات بعد</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card hms-table-card">
    <div class="card-header">آخر التفعيلات للمرضى</div>
    <div class="card-body">
        <table class="table hms-table">
            <thead><tr><th>المريض</th><th>الباقة</th><th>الخدمة</th><th>المستخدم/المسموح</th><th>ينتهي</th></tr></thead>
            <tbody>
            @forelse($usages as $usage)
                <tr>
                    <td>{{ optional($usage->patient)->name ?? '#'.$usage->patient_id }}</td>
                    <td>{{ optional($usage->group)->name }}</td>
                    <td>{{ optional($usage->service)->name }}</td>
                    <td>{{ $usage->quantity_used }}/{{ $usage->quantity_allowed }}</td>
                    <td>{{ optional($usage->expires_at)->format('Y-m-d') ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted text-center">لا توجد تفعيلات بعد</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('Dashboard.layouts.master')
@section('title') إصدار شهادة طبية @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card">
    <div class="card-body">
        <form action="{{ route('doctor.certificates.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>رقم المريض</label>
                <input type="number" name="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required value="{{ old('patient_id', optional($patient)->id) }}">
                @error('patient_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <small class="text-muted">رقم السجل من قائمة المرضى (مثال: 10)</small>
            </div>
            <div class="form-group">
                <label>نوع الشهادة</label>
                <select name="type" class="form-control @error('type') is-invalid @enderror" required id="certificate-type">
                    @foreach(\App\Models\MedicalCertificate::$typeLabels as $k => $v)
                        <option value="{{ $k }}" {{ old('type', 'sick_leave') === $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
                @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>العنوان</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" required value="{{ old('title') }}" placeholder="مثال: إجازة مرضية">
                @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>المحتوى</label>
                <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="6" required minlength="10" placeholder="اكتب نص الشهادة (10 أحرف على الأقل)...">{{ old('content') }}</textarea>
                @error('content')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="row">
                <div class="col-md-4 form-group" id="days-off-group">
                    <label>أيام الإجازة (للإجازة المرضية)</label>
                    <input type="number" name="days_off" class="form-control @error('days_off') is-invalid @enderror" min="1" value="{{ old('days_off') }}">
                    @error('days_off')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 form-group">
                    <label>من تاريخ</label>
                    <input type="date" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror" value="{{ old('valid_from') }}">
                    @error('valid_from')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 form-group">
                    <label>إلى تاريخ</label>
                    <input type="date" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror" value="{{ old('valid_until') }}">
                    @error('valid_until')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">إصدار وتحميل PDF</button>
            <a href="{{ route('doctor.certificates.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </form>
    </div>
</div>
<script>
(function () {
    var typeSelect = document.getElementById('certificate-type');
    var daysGroup = document.getElementById('days-off-group');
    function toggleDaysOff() {
        if (!typeSelect || !daysGroup) return;
        daysGroup.style.display = typeSelect.value === 'sick_leave' ? '' : 'none';
    }
    if (typeSelect) {
        typeSelect.addEventListener('change', toggleDaysOff);
        toggleDaysOff();
    }
})();
</script>
@endsection

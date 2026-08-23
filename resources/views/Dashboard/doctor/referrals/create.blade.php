@extends('Dashboard.layouts.master')
@section('title') تحويل مريض @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card">
    <div class="card-body">
        @if($doctors->isEmpty())
            <div class="alert alert-warning">لا يوجد أطباء آخرون متاحون للتحويل حالياً.</div>
        @endif
        <form action="{{ route('doctor.referrals.store') }}" method="POST">
            @csrf
            @if(request('diagnostic_id'))
                <input type="hidden" name="diagnostic_id" value="{{ request('diagnostic_id') }}">
            @endif
            <div class="form-group">
                <label>المريض <span class="text-danger">*</span></label>
                <select name="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required>
                    <option value="">— اختر المريض —</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id', request('patient_id')) == $patient->id ? 'selected' : '' }}>
                            {{ $patient->name }} — #{{ $patient->id }} @if($patient->Phone) ({{ $patient->Phone }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('patient_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>الطبيب المحوّل إليه <span class="text-danger">*</span></label>
                <select name="to_doctor_id" class="form-control @error('to_doctor_id') is-invalid @enderror" required @if($doctors->isEmpty()) disabled @endif>
                    <option value="">— اختر الطبيب —</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ old('to_doctor_id') == $doc->id ? 'selected' : '' }}>
                            {{ $doc->name }} — {{ optional($doc->section)->name ?? 'بدون قسم' }}
                        </option>
                    @endforeach
                </select>
                @error('to_doctor_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>سبب التحويل <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required minlength="3" placeholder="اكتب سبب التحويل (3 أحرف على الأقل)">{{ old('reason') }}</textarea>
                @error('reason')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label>ملاحظات</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary" @if($doctors->isEmpty()) disabled @endif>إرسال التحويل</button>
            <a href="{{ route('doctor.referrals.index') }}" class="btn btn-secondary">رجوع</a>
        </form>
    </div>
</div>
@endsection

@extends('Dashboard.layouts.master')
@section('title') تحويل مريض @endsection
@section('content')
<div class="card hms-table-card">
    <div class="card-body">
        <form action="{{ route('doctor.referrals.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>رقم المريض</label>
                <input type="number" name="patient_id" class="form-control" required value="{{ old('patient_id', request('patient_id')) }}">
            </div>
            <div class="form-group">
                <label>الطبيب المحوّل إليه</label>
                <select name="to_doctor_id" class="form-control" required>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->name }} — {{ optional($doc->section)->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>سبب التحويل</label>
                <textarea name="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
            </div>
            <div class="form-group">
                <label>ملاحظات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">إرسال التحويل</button>
            <a href="{{ route('doctor.referrals.index') }}" class="btn btn-secondary">رجوع</a>
        </form>
    </div>
</div>
@endsection

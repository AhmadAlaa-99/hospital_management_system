@extends('Dashboard.layouts.master')
@section('title') إصدار شهادة طبية @endsection
@section('content')
<div class="card hms-table-card">
    <div class="card-body">
        <form action="{{ route('doctor.certificates.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>رقم المريض</label>
                <input type="number" name="patient_id" class="form-control" required value="{{ old('patient_id', optional($patient)->id) }}">
            </div>
            <div class="form-group">
                <label>نوع الشهادة</label>
                <select name="type" class="form-control" required>
                    @foreach(\App\Models\MedicalCertificate::$typeLabels as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>العنوان</label>
                <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label>المحتوى</label>
                <textarea name="content" class="form-control" rows="6" required>{{ old('content') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>أيام الإجازة (للإجازة المرضية)</label>
                    <input type="number" name="days_off" class="form-control" min="1">
                </div>
                <div class="col-md-4 form-group">
                    <label>من تاريخ</label>
                    <input type="date" name="valid_from" class="form-control">
                </div>
                <div class="col-md-4 form-group">
                    <label>إلى تاريخ</label>
                    <input type="date" name="valid_until" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">إصدار وتحميل PDF</button>
        </form>
    </div>
</div>
@endsection

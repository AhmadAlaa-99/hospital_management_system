@extends('Dashboard.layouts.master')
@section('title') تقرير التأمين @endsection
@section('content')
<div class="card mb-3"><div class="card-body">
    <form method="GET" class="form-inline">
        <select name="insurance_id" class="form-control mr-2">
            <option value="">كل الشركات</option>
            @foreach($insurances as $ins)
                <option value="{{ $ins->id }}" {{ $insuranceId == $ins->id ? 'selected' : '' }}>{{ $ins->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary">عرض</button>
    </form>
</div></div>
<div class="row mb-3">
    <div class="col-md-3"><div class="card p-3">عدد المطالبات: <strong>{{ $summary['count'] }}</strong></div></div>
    <div class="col-md-3"><div class="card p-3">الإجمالي: <strong>{{ number_format($summary['total'], 2) }}</strong></div></div>
    <div class="col-md-3"><div class="card p-3">تحمل الشركات: <strong>{{ number_format($summary['company'], 2) }}</strong></div></div>
    <div class="col-md-3"><div class="card p-3">تحمل المرضى: <strong>{{ number_format($summary['patient'], 2) }}</strong></div></div>
</div>
<div class="card hms-table-card"><div class="card-body">
    <table class="table hms-table">
        <thead><tr><th>التاريخ</th><th>المريض</th><th>الشركة</th><th>الإجمالي</th><th>الشركة</th><th>المريض</th><th>الحالة</th></tr></thead>
        <tbody>
        @foreach($claims as $claim)
            <tr>
                <td>{{ $claim->claim_date->format('Y-m-d') }}</td>
                <td>{{ optional($claim->patient)->name }}</td>
                <td>{{ optional($claim->insurance)->name }}</td>
                <td>{{ number_format($claim->total_amount, 2) }}</td>
                <td>{{ number_format($claim->company_amount, 2) }}</td>
                <td>{{ number_format($claim->patient_amount, 2) }}</td>
                <td>{{ \App\Models\InsuranceClaim::$statusLabels[$claim->status] ?? $claim->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div></div>
@endsection

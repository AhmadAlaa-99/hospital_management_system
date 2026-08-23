@extends('Dashboard.layouts.master')
@section('title') مطالبات التأمين @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0">مطالبات التأمين</h4></div>
    <div class="d-flex" style="gap:8px">
        <a href="{{ route('insurance-claims.report') }}" class="btn btn-info btn-sm"><i class="fas fa-chart-bar ml-1"></i> تقرير الشركات</a>
        <a href="{{ route('export.insurance-claims') }}" class="btn btn-success btn-sm"><i class="fas fa-file-export ml-1"></i> تصدير CSV</a>
    </div>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')

@php
    $statusBadges = [
        'pending' => 'warning',
        'approved' => 'info',
        'rejected' => 'danger',
        'paid' => 'success',
    ];
@endphp

<div class="card hms-table-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table hms-table" id="insurance-claims-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>رقم السجل</th>
                    <th>المريض</th>
                    <th>شركة التأمين</th>
                    <th>الإجمالي</th>
                    <th>تحمل الشركة</th>
                    <th>تحمل المريض</th>
                    <th>الحالة</th>
                    <th style="min-width:200px">تحديث الحالة</th>
                </tr>
                </thead>
                <tbody>
                @foreach($claims as $claim)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $claim->id }}</td>
                        <td>{{ optional($claim->patient)->name }}</td>
                        <td>{{ optional($claim->insurance)->name }}</td>
                        <td>{{ number_format($claim->total_amount, 2) }}</td>
                        <td class="text-info">{{ number_format($claim->company_amount, 2) }}</td>
                        <td>{{ number_format($claim->patient_amount, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $statusBadges[$claim->status] ?? 'secondary' }}">
                                {{ \App\Models\InsuranceClaim::$statusLabels[$claim->status] ?? $claim->status }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('insurance-claims.status', $claim) }}" method="POST" class="hms-inline-form">
                                @csrf @method('PUT')
                                <select name="status" class="form-control form-control-sm">
                                    @foreach(\App\Models\InsuranceClaim::$statusLabels as $k => $v)
                                        <option value="{{ $k }}" {{ $claim->status === $k ? 'selected' : '' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">حفظ</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $claims->links() }}
    </div>
</div>
@endsection

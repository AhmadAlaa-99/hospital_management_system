@extends('Dashboard.layouts.master')
@section('title') التحويلات بين التخصصات @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card mb-3">
    <div class="card-body d-flex justify-content-between">
        <h5 class="mb-0">التحويلات الواردة والصادرة</h5>
        <a href="{{ route('doctor.referrals.create') }}" class="btn btn-primary btn-sm">تحويل جديد</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card hms-table-card"><div class="card-header">التحويلات الواردة</div><div class="card-body">
            <table class="table hms-table">
                <thead><tr><th>المريض</th><th>من</th><th>الحالة</th><th></th></tr></thead>
                <tbody>
                @forelse($received as $ref)
                    <tr>
                        <td>#{{ $ref->patient_id }}</td>
                        <td>{{ optional($ref->fromDoctor)->name }}</td>
                        <td>{{ \App\Models\Referral::$statusLabels[$ref->status] ?? $ref->status }}</td>
                        <td>
                            @if($ref->status === 'pending')
                                <form action="{{ route('doctor.referrals.accept', $ref) }}" method="POST" class="d-inline">@csrf<button class="btn btn-xs btn-success">قبول</button></form>
                                <form action="{{ route('doctor.referrals.reject', $ref) }}" method="POST" class="d-inline">@csrf<button class="btn btn-xs btn-danger">رفض</button></form>
                            @elseif($ref->status === 'accepted')
                                <form action="{{ route('doctor.referrals.complete', $ref) }}" method="POST" class="d-inline">@csrf<button class="btn btn-xs btn-primary">إكمال</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center">لا توجد</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $received->links() }}
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card hms-table-card"><div class="card-header">التحويلات الصادرة</div><div class="card-body">
            <table class="table hms-table">
                <thead><tr><th>المريض</th><th>إلى</th><th>التخصص</th><th>الحالة</th></tr></thead>
                <tbody>
                @forelse($sent as $ref)
                    <tr>
                        <td>#{{ $ref->patient_id }}</td>
                        <td>{{ optional($ref->toDoctor)->name }}</td>
                        <td>{{ optional($ref->toSection)->name }}</td>
                        <td>{{ \App\Models\Referral::$statusLabels[$ref->status] ?? $ref->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center">لا توجد</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $sent->links() }}
        </div></div>
    </div>
</div>
@endsection

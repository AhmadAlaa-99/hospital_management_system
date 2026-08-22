@extends('Dashboard.layouts.master')
@section('title') خطط المتابعة @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card">
    <div class="card-body">
        <table class="table hms-table">
            <thead><tr><th>المريض</th><th>التاريخ</th><th>الحالة</th><th>ملاحظات</th><th></th></tr></thead>
            <tbody>
            @forelse($plans as $plan)
                <tr>
                    <td>#{{ $plan->patient_id }}</td>
                    <td>{{ $plan->follow_up_date->format('Y-m-d') }}</td>
                    <td>{{ \App\Models\FollowUpPlan::$statusLabels[$plan->status] ?? $plan->status }}</td>
                    <td>{{ $plan->notes }}</td>
                    <td>
                        @if($plan->status === 'scheduled')
                            <form action="{{ route('doctor.follow-ups.appointment', $plan) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-primary">موعد</button></form>
                            <form action="{{ route('doctor.follow-ups.complete', $plan) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-success">تم</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">لا توجد خطط متابعة</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $plans->links() }}
    </div>
</div>
@endsection

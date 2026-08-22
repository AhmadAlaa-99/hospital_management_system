@extends('Dashboard.layouts.master')
@section('title') متابعاتي @endsection
@section('content')
<div class="card hms-table-card"><div class="card-body">
    <table class="table hms-table">
        <thead><tr><th>الطبيب</th><th>التخصص</th><th>التاريخ</th><th>الحالة</th></tr></thead>
        <tbody>
        @forelse($plans as $plan)
            <tr>
                <td>{{ optional($plan->doctor)->name }}</td>
                <td>{{ optional($plan->section)->name }}</td>
                <td>{{ $plan->follow_up_date->format('Y-m-d') }}</td>
                <td>{{ \App\Models\FollowUpPlan::$statusLabels[$plan->status] ?? $plan->status }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted">لا توجد متابعات</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $plans->links() }}
</div></div>
@endsection

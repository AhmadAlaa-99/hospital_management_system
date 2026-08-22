@extends('Dashboard.layouts.master')
@section('title') التحويلات @endsection
@section('content')
<div class="card hms-table-card"><div class="card-body">
    <table class="table hms-table">
        <thead><tr><th>#</th><th>المريض</th><th>من</th><th>إلى</th><th>الحالة</th><th>التاريخ</th></tr></thead>
        <tbody>
        @foreach($referrals as $ref)
            <tr>
                <td>{{ $ref->id }}</td>
                <td>#{{ $ref->patient_id }}</td>
                <td>{{ optional($ref->fromDoctor)->name }} ({{ optional($ref->fromSection)->name }})</td>
                <td>{{ optional($ref->toDoctor)->name }} ({{ optional($ref->toSection)->name }})</td>
                <td>{{ \App\Models\Referral::$statusLabels[$ref->status] ?? $ref->status }}</td>
                <td>{{ $ref->created_at->format('Y-m-d') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $referrals->links() }}
</div></div>
@endsection

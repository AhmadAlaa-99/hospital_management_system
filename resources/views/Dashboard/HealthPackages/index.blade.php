@extends('Dashboard.layouts.master')
@section('title') باقات الفحص @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card mb-3"><div class="card-header">تفعيل باقة لمريض</div><div class="card-body">
    <form action="{{ route('health-packages.activate') }}" method="POST" class="form-inline">@csrf
        <input type="number" name="patient_id" class="form-control mr-2" placeholder="رقم المريض" required>
        <select name="group_id" class="form-control mr-2" required>
            @foreach($packages as $pkg)
                <option value="{{ $pkg->id }}">{{ $pkg->name }} ({{ $pkg->validity_days }} يوم)</option>
            @endforeach
        </select>
        <button class="btn btn-primary">تفعيل</button>
    </form>
</div></div>
<div class="card hms-table-card"><div class="card-body">
    <table class="table hms-table">
        <thead><tr><th>الباقة</th><th>النوع</th><th>الصلاحية</th><th>الخدمات</th></tr></thead>
        <tbody>
        @forelse($packages as $pkg)
            <tr>
                <td>{{ $pkg->name }}</td>
                <td>{{ $pkg->package_type ?? '—' }}</td>
                <td>{{ $pkg->validity_days }} يوم</td>
                <td>{{ $pkg->service_group->count() }} خدمة</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-muted text-center">فعّل is_health_package على مجموعة خدمات من صفحة المجموعات</td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
@endsection

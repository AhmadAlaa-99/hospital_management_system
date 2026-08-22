@extends('Dashboard.layouts.master')
@section('title') سجل النشاط @endsection
@section('content')
<div class="card hms-table-card">
    <div class="card-body">
        <table class="table hms-table table-sm">
            <thead><tr><th>الوقت</th><th>المستخدم</th><th>العملية</th><th>النموذج</th><th>IP</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $log->user_type }} #{{ $log->user_id }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ class_basename($log->model_type ?? '') }} #{{ $log->model_id }}</td>
                    <td>{{ $log->ip_address }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted text-center">لا يوجد سجل</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $logs->links() }}
    </div>
</div>
@endsection

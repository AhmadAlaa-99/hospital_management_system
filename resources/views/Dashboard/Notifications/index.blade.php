@extends('Dashboard.layouts.master')
@section('title') الإشعارات @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <h4 class="content-title mb-0">جميع الإشعارات</h4>
    </div>
    @if($unreadCount > 0)
        <a href="{{ route('notifications.readAll') }}" class="btn btn-sm btn-warning">تعليم الكل كمقروء</a>
    @endif
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($notifications as $notification)
                <a href="{{ route('notifications.read', $notification->id) }}"
                   class="list-group-item list-group-item-action d-flex align-items-start {{ $notification->reader_status ? '' : 'bg-light' }}">
                    <span class="avatar avatar-md brround ml-3 bg-{{ $notification->reader_status ? 'secondary' : 'teal' }}">
                        <i class="la la-bell text-white"></i>
                    </span>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $notification->message }}</strong>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                        @unless($notification->reader_status)
                            <span class="badge badge-success mt-1">جديد</span>
                        @endunless
                    </div>
                </a>
            @empty
                <div class="p-5 text-center text-muted">لا توجد إشعارات</div>
            @endforelse
        </div>
    </div>
</div>
{{ $notifications->links() }}
@endsection

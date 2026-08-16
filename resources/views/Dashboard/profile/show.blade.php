@extends('Dashboard.layouts.master')
@section('title')
    الملف الشخصي
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الملف الشخصي</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ عرض البيانات</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-lg-4">
            <div class="card hms-table-card">
                <div class="card-body text-center">
                    <img src="{{ URL::asset('Dashboard/img/faces/6.jpg') }}" class="hms-avatar mb-3" style="width:90px;height:90px" alt="avatar">
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <span class="hms-badge hms-badge-info">{{ $guard }}</span>
                    <div class="mt-4">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-hms-primary">تعديل الملف الشخصي</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card hms-table-card">
                <div class="card-header"><h5 class="mb-0">معلومات الحساب</h5></div>
                <div class="card-body">
                    <table class="table hms-table mb-0">
                        <tr><th width="30%">الاسم</th><td>{{ $user->name }}</td></tr>
                        <tr><th>البريد الالكتروني</th><td>{{ $user->email }}</td></tr>
                        @if(isset($user->phone))
                            <tr><th>الهاتف</th><td>{{ $user->phone }}</td></tr>
                        @endif
                        <tr><th>تاريخ الإنشاء</th><td>{{ optional($user->created_at)->diffForHumans() }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection

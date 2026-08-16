@extends('Dashboard.layouts.master')
@section('title')
    تعديل الملف الشخصي
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الملف الشخصي</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('Dashboard.messages_alert')
    <div class="row row-sm">
        <div class="col-xl-8 col-lg-10">
            <div class="card hms-form-card">
                <div class="card-header"><h5 class="mb-0">تعديل البيانات</h5></div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" class="hms-form-box">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>الاسم</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label>البريد الالكتروني</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                            @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label>كلمة المرور الجديدة (اختياري)</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password">
                            @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label>تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-hms-primary">حفظ التعديلات</button>
                        <a href="{{ route('profile.show') }}" class="btn btn-light">رجوع</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection

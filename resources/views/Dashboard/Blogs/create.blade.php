@extends('Dashboard.layouts.master')
@section('title') مقال جديد @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">المدونة</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ مقال جديد</span>
        </div>
    </div>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-form-card">
    <div class="card-header">
        <h5 class="mb-0 hms-form-title">إضافة مقال جديد</h5>
        <p class="mb-0 text-muted tx-13">أدخل بيانات المقال بما يتوافق مع هوية المستشفى</p>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" class="hms-form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        <label for="title">العنوان</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="excerpt">ملخص</label>
                        <textarea name="excerpt" id="excerpt" class="form-control" rows="2">{{ old('excerpt') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="body">المحتوى</label>
                        <textarea name="body" id="body" class="form-control" rows="10" required>{{ old('body') }}</textarea>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hms-form-side">
                        <div class="form-group">
                            <label for="author">الكاتب</label>
                            <input type="text" name="author" id="author" class="form-control" value="{{ old('author', 'إدارة المستشفى') }}">
                        </div>
                        <div class="form-group">
                            <label for="image">صورة الغلاف</label>
                            <input type="file" name="image" id="image" class="form-control-file hms-file-input" accept="image/*">
                        </div>
                        <div class="custom-control custom-checkbox hms-form-check">
                            <input type="checkbox" name="is_published" value="1" class="custom-control-input" id="pub" checked>
                            <label class="custom-control-label" for="pub">نشر المقال فوراً</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hms-form-actions">
                <button type="submit" class="btn btn-primary btn-hms-primary">حفظ المقال</button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </form>
    </div>
</div>
@endsection

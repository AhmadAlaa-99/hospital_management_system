@extends('Dashboard.layouts.master')
@section('title') تعديل مقال @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">المدونة</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ تعديل مقال</span>
        </div>
    </div>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-form-card">
    <div class="card-header">
        <h5 class="mb-0 hms-form-title">تعديل المقال</h5>
        <p class="mb-0 text-muted tx-13">{{ $blog->title }}</p>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.blogs.update', $blog) }}" method="POST" enctype="multipart/form-data" class="hms-form">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-lg-8">
                    <div class="form-group">
                        <label for="title">العنوان</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $blog->title) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="excerpt">ملخص</label>
                        <textarea name="excerpt" id="excerpt" class="form-control" rows="2">{{ old('excerpt', $blog->excerpt) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="body">المحتوى</label>
                        <textarea name="body" id="body" class="form-control" rows="10" required>{{ old('body', $blog->body) }}</textarea>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="hms-form-side">
                        <div class="form-group">
                            <label for="author">الكاتب</label>
                            <input type="text" name="author" id="author" class="form-control" value="{{ old('author', $blog->author) }}">
                        </div>
                        @if($blog->image)
                            <div class="form-group">
                                <label>الصورة الحالية</label>
                                <div class="hms-form-preview">
                                    <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}">
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="image">صورة جديدة</label>
                            <input type="file" name="image" id="image" class="form-control-file hms-file-input" accept="image/*">
                        </div>
                        <div class="custom-control custom-checkbox hms-form-check">
                            <input type="checkbox" name="is_published" value="1" class="custom-control-input" id="pub" {{ $blog->is_published ? 'checked' : '' }}>
                            <label class="custom-control-label" for="pub">منشور</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hms-form-actions">
                <button type="submit" class="btn btn-primary btn-hms-primary">تحديث المقال</button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('Dashboard.layouts.master')
@section('title') المدونة @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0">المقالات</h4></div>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-hms-primary">مقال جديد</a>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table hms-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>العنوان</th>
                        <th>الكاتب</th>
                        <th>منشور</th>
                        <th>المشاهدات</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($blogs as $blog)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $blog->title }}</td>
                        <td>{{ $blog->author }}</td>
                        <td>
                            <span class="hms-badge {{ $blog->is_published ? 'hms-badge-success' : 'hms-badge-danger' }}">
                                {{ $blog->is_published ? 'نعم' : 'لا' }}
                            </span>
                        </td>
                        <td>{{ $blog->views }}</td>
                        <td class="text-center">
                            <div class="hms-actions">
                                <a href="{{ route('blogs.show', $blog->slug) }}"
                                   class="hms-action-btn hms-action-btn--view"
                                   target="_blank"
                                   title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.blogs.edit', $blog) }}"
                                   class="hms-action-btn hms-action-btn--edit"
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف المقال؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="hms-action-btn hms-action-btn--delete" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $blogs->links() }}
    </div>
</div>
@endsection

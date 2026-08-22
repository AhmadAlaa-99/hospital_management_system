@extends('Dashboard.layouts.master')
@section('title') ملفاتي الطبية @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card mb-3"><div class="card-header">رفع ملف طبي سابق</div><div class="card-body">
    <form action="{{ route('patient.external-records.store') }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="row">
            <div class="col-md-4"><input name="title" class="form-control" placeholder="عنوان الملف" required></div>
            <div class="col-md-3">
                <select name="type" class="form-control" required>
                    @foreach(\App\Models\ExternalRecord::$typeLabels as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3"><input type="file" name="file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png"></div>
            <div class="col-md-2"><button class="btn btn-primary btn-block">رفع</button></div>
        </div>
    </form>
</div></div>
<div class="card hms-table-card"><div class="card-body">
    <table class="table hms-table">
        <thead><tr><th>العنوان</th><th>النوع</th><th>التاريخ</th><th></th></tr></thead>
        <tbody>
        @forelse($records as $rec)
            <tr>
                <td>{{ $rec->title }}</td>
                <td>{{ \App\Models\ExternalRecord::$typeLabels[$rec->type] ?? $rec->type }}</td>
                <td>{{ $rec->created_at->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('patient.external-records.download', $rec) }}" class="btn btn-sm btn-success">تحميل</a>
                    <form action="{{ route('patient.external-records.destroy', $rec) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">حذف</button></form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center text-muted">لا توجد ملفات</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $records->links() }}
</div></div>
@endsection

@extends('Dashboard.layouts.master')
@section('title') مراجعات المرضى @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <h4 class="content-title mb-0">مراجعات المرضى للصفحة الرئيسية</h4>
    </div>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')

<div class="row mb-3">
    <div class="col-md-4"><div class="card p-3 border-warning"><small class="text-muted">بانتظار الموافقة</small><h4 class="text-warning mb-0">{{ $counts['pending'] }}</h4></div></div>
    <div class="col-md-4"><div class="card p-3 border-success"><small class="text-muted">منشورة</small><h4 class="text-success mb-0">{{ $counts['approved'] }}</h4></div></div>
    <div class="col-md-4"><div class="card p-3 border-danger"><small class="text-muted">مرفوضة</small><h4 class="text-danger mb-0">{{ $counts['rejected'] }}</h4></div></div>
</div>

<div class="card hms-table-card">
    <div class="card-body">
        <div class="mb-3 d-flex flex-wrap" style="gap:8px">
            <a href="{{ route('patient-testimonials.index', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">بانتظار الموافقة</a>
            <a href="{{ route('patient-testimonials.index', ['status' => 'approved']) }}" class="btn btn-sm {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">منشورة</a>
            <a href="{{ route('patient-testimonials.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">مرفوضة</a>
            <a href="{{ route('patient-testimonials.index', ['status' => 'all']) }}" class="btn btn-sm {{ $status === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">الكل</a>
        </div>

        <div class="table-responsive">
            <table class="table hms-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>المريض</th>
                    <th>الطبيب</th>
                    <th>التقييم</th>
                    <th>المراجعة</th>
                    <th>الحالة</th>
                    <th style="min-width:220px">إجراء</th>
                </tr>
                </thead>
                <tbody>
                @forelse($testimonials as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ optional($item->patient)->name }}</td>
                        <td>{{ optional($item->doctor)->name }}</td>
                        <td>
                            <span class="text-warning">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $item->rating ? '' : '-o' }}"></i>
                                @endfor
                            </span>
                            <small class="text-muted d-block">{{ $item->rating }}/5</small>
                        </td>
                        <td style="max-width:320px">{{ Str::limit($item->comment, 120) }}</td>
                        <td>
                            @php($label = \App\Models\DoctorRating::$homepageStatusLabels[$item->homepage_status] ?? $item->homepage_status)
                            <span class="badge badge-{{ $item->homepage_status === 'approved' ? 'success' : ($item->homepage_status === 'pending' ? 'warning' : 'secondary') }}">{{ $label }}</span>
                        </td>
                        <td>
                            <div class="hms-actions hms-actions--stack">
                                @if($item->homepage_status === \App\Models\DoctorRating::HOMEPAGE_PENDING)
                                    <form action="{{ route('patient-testimonials.approve', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="hms-action-btn hms-action-btn--edit" title="موافقة ونشر"><i class="fas fa-check"></i></button>
                                    </form>
                                    <form action="{{ route('patient-testimonials.reject', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="hms-action-btn hms-action-btn--delete" title="رفض"><i class="fas fa-times"></i></button>
                                    </form>
                                @elseif($item->homepage_status === \App\Models\DoctorRating::HOMEPAGE_APPROVED)
                                    <form action="{{ route('patient-testimonials.unpublish', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">إخفاء</button>
                                    </form>
                                @else
                                    <form action="{{ route('patient-testimonials.approve', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">إعادة نشر</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">لا توجد مراجعات في هذا القسم</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $testimonials->links() }}
    </div>
</div>
@endsection

@extends('Dashboard.layouts.master')
@section('css')
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">مواعيدي</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة المواعيد</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card hms-table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap hms-table" id="example1">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>القسم</th>
                                <th>الطبيب</th>
                                <th>موعد الزيارة</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                                <th>تقييم</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($appointments as $appointment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ optional($appointment->section)->name }}</td>
                                    <td>{{ optional($appointment->doctor)->name }}</td>
                                    <td>{{ $appointment->appointment ?: '-' }}</td>
                                    <td>
                                        @if($appointment->type === 'مؤكد')
                                            <span class="hms-badge hms-badge-success">مؤكد</span>
                                        @elseif($appointment->type === 'مرفوض')
                                            <span class="hms-badge hms-badge-danger">مرفوض</span>
                                        @elseif($appointment->type === 'منتهي')
                                            <span class="hms-badge hms-badge-info">منتهي</span>
                                        @else
                                            <span class="hms-badge hms-badge-warning">غير مؤكد</span>
                                        @endif
                                    </td>
                                    <td>{{ $appointment->notes }}</td>
                                    <td>
                                        @php $existingRating = $appointment->doctorRating ?? \App\Models\DoctorRating::where('appointment_id', $appointment->id)->first(); @endphp
                                        @if($appointment->type === 'منتهي' && !$existingRating)
                                            <a href="{{ route('patient.rate.create', $appointment) }}" class="btn btn-sm btn-warning">قيّم الطبيب</a>
                                        @elseif($existingRating)
                                            <span class="text-success d-block">تم التقييم ✓</span>
                                            @if($existingRating->share_on_homepage)
                                                @if($existingRating->homepage_status === \App\Models\DoctorRating::HOMEPAGE_PENDING)
                                                    <small class="text-warning">المراجعة بانتظار الموافقة</small>
                                                @elseif($existingRating->homepage_status === \App\Models\DoctorRating::HOMEPAGE_APPROVED)
                                                    <small class="text-success">منشورة في الموقع</small>
                                                @elseif($existingRating->homepage_status === \App\Models\DoctorRating::HOMEPAGE_REJECTED)
                                                    <small class="text-muted">لم تُنشر المراجعة</small>
                                                @endif
                                            @endif
                                        @else — @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">لا توجد مواعيد مرتبطة بحسابك</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection

@extends('Dashboard.layouts.master')
@section('css')
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">مواعيدي</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ المواعيد النشطة</span>
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
                                <th>اسم المريض</th>
                                <th>البريد</th>
                                <th>الهاتف</th>
                                <th>القسم</th>
                                <th>موعد الزيارة</th>
                                <th>الحالة</th>
                                <th>ملاحظات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($appointments as $appointment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $appointment->name }}</td>
                                    <td>{{ $appointment->email }}</td>
                                    <td>{{ $appointment->phone }}</td>
                                    <td>{{ optional($appointment->section)->name }}</td>
                                    <td>{{ $appointment->appointment ?: '-' }}</td>
                                    <td>
                                        @if($appointment->type === 'مؤكد')
                                            <span class="hms-badge hms-badge-success">مؤكد</span>
                                        @else
                                            <span class="hms-badge hms-badge-warning">غير مؤكد</span>
                                        @endif
                                    </td>
                                    <td>{{ $appointment->notes }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">لا توجد مواعيد</td></tr>
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

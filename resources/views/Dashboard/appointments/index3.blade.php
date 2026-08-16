@extends('Dashboard.layouts.master')
@section('css')
    <link href="{{URL::asset('dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المواعيد</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ المواعيد المنتهية</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('Dashboard.messages_alert')
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
                                <th>البريد الالكتروني</th>
                                <th>القسم</th>
                                <th>الدكتور</th>
                                <th>تاريخ الموعد</th>
                                <th>الهاتف</th>
                                <th>الحالة</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($appointments as $appointment)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $appointment->name }}</td>
                                    <td>{{ $appointment->email }}</td>
                                    <td>{{ optional($appointment->section)->name }}</td>
                                    <td>{{ optional($appointment->doctor)->name }}</td>
                                    <td>{{ $appointment->appointment ?? '-' }}</td>
                                    <td>{{ $appointment->phone }}</td>
                                    <td>
                                        @if($appointment->type === 'مرفوض')
                                            <span class="hms-badge hms-badge-danger">مرفوض</span>
                                        @else
                                            <span class="hms-badge hms-badge-info">منتهي</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">لا توجد مواعيد منتهية</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('dashboard/plugins/notify/js/notifIt.js')}}"></script>
@endsection

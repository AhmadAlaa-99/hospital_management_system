@extends('Dashboard.layouts.master')
@section('css')

@endsection
@section('title')
    فاتورة مجموعة خدمات
@stop
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ فاتورة
                    مجموعة خدمات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @include('Dashboard.partials.help-box', [
        'title' => 'فاتورة مجموعة خدمات',
        'body' => 'اختر <strong>مجموعة خدمات</strong> (باقة) مُعرّفة مسبقاً من «الخدمات → مجموعة خدمات»، ثم المريض والطبيب. يمكنك الإضافة والتعديل والطباعة والحذف من الأزرار في الجدول.'
    ])

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card hms-table-card">
                <div class="card-body">
                    <livewire:group-invoices />
                </div>
            </div>
        </div>

    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')

@endsection

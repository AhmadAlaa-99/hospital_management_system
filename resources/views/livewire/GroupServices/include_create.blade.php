@extends('Dashboard.layouts.master')
@section('css')
  
@endsection
@section('title')
    مجموعة خدمات
@stop
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الخدمات</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ مجموعة خدمات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

    @include('Dashboard.partials.help-box', [
        'title' => 'مجموعة خدمات — أين تُستخدم؟',
        'body' => '<strong>1. هنا:</strong> تنشئ باقة (مجموعة) من خدمات مفردة مع خصم وضريبة.<br>
        <strong>2. الفواتير → فاتورة مجموعة خدمات:</strong> تُستخدم عند إصدار فاتورة للمريض.<br>
        <strong>خطوات الإضافة:</strong> اسم المجموعة → «إضافة خدمة فرعية» → اختر الخدمة → «تأكيد» على السطر → «تأكيد البيانات».'
    ])

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card hms-form-card">
                <div class="card-body">
                    <livewire:create-group-services/>
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

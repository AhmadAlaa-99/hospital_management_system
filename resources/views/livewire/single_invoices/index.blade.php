@extends('Dashboard.layouts.master')
@section('css')
@endsection
@section('title')
    فاتورة خدمة مفردة
@stop
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الفواتير</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ فاتورة خدمة مفردة</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('Dashboard.partials.help-box', [
        'title' => 'خدمة مفردة vs فاتورة — أين تُستخدم؟',
        'body' => '<strong>خدمة مفردة</strong> (القائمة ← الخدمات): تعريف سعر خدمة واحدة (أشعة، تحليل...).<br>
        <strong>هذه الصفحة — فاتورة خدمة مفردة:</strong> إصدار فاتورة للمريض بخدمة واحدة (إضافة / تعديل / طباعة / حذف).<br>
        <strong>مجموعة خدمات:</strong> باقة عدة خدمات — تُفوتر من «فاتورة مجموعة خدمات».'
    ])

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card hms-table-card">
                <div class="card-body">
                    <livewire:single-invoices/>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection

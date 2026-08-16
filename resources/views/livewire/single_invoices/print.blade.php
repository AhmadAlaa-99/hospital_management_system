@extends('Dashboard.layouts.master')
@section('title')
    طباعه الفواتير
@stop
@section('css')
    <style>
        @media print {
            #print_Button { display: none; }
        }
    </style>
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الفواتير</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ طباعه الفاتورة #{{ $invoice->id }}</span>
        </div>
    </div>
</div>
@endsection
@section('content')
@php($site = \App\Models\SiteSetting::current())
<div class="row row-sm">
    <div class="col-md-12 col-xl-12">
        <div class="main-content-body-invoice" id="print">
            <div class="card card-invoice">
                <div class="card-body">
                    <div class="invoice-header">
                        <h1 class="invoice-title">فاتورة خدمة مفردة</h1>
                        @include('Dashboard.partials.voucher-header')
                    </div>
                    <div class="row mg-t-20">
                        <div class="col-md">
                            <label class="tx-gray-600">معلومات الفاتورة</label>
                            <p class="invoice-info-row"><span>رقم الفاتورة</span> <span>#{{ $invoice->id }}</span></p>
                            <p class="invoice-info-row"><span>اسم الخدمة</span> <span>{{ optional($invoice->Service)->name }}</span></p>
                            <p class="invoice-info-row"><span>اسم المريض</span> <span>{{ optional($invoice->Patient)->name }}</span></p>
                            <p class="invoice-info-row"><span>تاريخ الفاتورة</span> <span>{{ $invoice->invoice_date }}</span></p>
                            <p class="invoice-info-row"><span>الدكتور</span> <span>{{ optional($invoice->Doctor)->name }}</span></p>
                            <p class="invoice-info-row"><span>القسم</span> <span>{{ optional($invoice->Section)->name }}</span></p>
                        </div>
                    </div>
                    <div class="table-responsive mg-t-40">
                        <table class="table table-invoice border text-md-nowrap mb-0">
                            <thead>
                            <tr>
                                <th class="wd-20p">#</th>
                                <th class="wd-40p">اسم الخدمة</th>
                                <th class="tx-center">سعر الخدمة</th>
                                <th class="tx-right">نوع الفاتورة</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td class="tx-12">{{ optional($invoice->Service)->name }}</td>
                                <td class="tx-center">{{ number_format($invoice->price, 2) }}</td>
                                <td class="tx-right">{{ $invoice->type == 1 ? 'نقدي' : 'اجل' }}</td>
                            </tr>
                            <tr>
                                <td class="valign-middle" colspan="2" rowspan="4"></td>
                                <td class="tx-right">الاجمالي</td>
                                <td class="tx-right" colspan="2">{{ number_format($invoice->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="tx-right">قيمة الخصم</td>
                                <td class="tx-right" colspan="2">{{ number_format($invoice->discount_value, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="tx-right">نسبة الضريبة</td>
                                <td class="tx-right" colspan="2">% {{ $invoice->tax_rate }}</td>
                            </tr>
                            <tr>
                                <td class="tx-right tx-uppercase tx-bold tx-inverse">الاجمالي شامل الضريبة</td>
                                <td class="tx-right" colspan="2">
                                    <h4 class="tx-primary tx-bold">{{ number_format($invoice->total_with_tax, 2) }}</h4>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <hr class="mg-b-40">
                    <a href="{{ route('single_invoices') }}" class="btn btn-secondary float-left mt-3 ml-2">رجوع</a>
                    <a href="#" class="btn btn-danger float-left mt-3 mr-2" id="print_Button" onclick="printDiv()">
                        <i class="mdi mdi-printer ml-1"></i>طباعة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    function printDiv() {
        var printContents = document.getElementById('print').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>
@endsection

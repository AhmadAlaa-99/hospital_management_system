@extends('Dashboard.layouts.master')
@section('title')
    {{trans('Dashboard/main-sidebar_trans.sections')}}
@stop
@section('css')
    <link href="{{URL::asset('Dashboard/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">عمليات المريض</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الفواتير</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="row row-sm">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table style="text-align: center" class="table text-md-nowrap" id="example1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>تاريخ الفاتورة</th>
                            <th>اسم الدكتور</th>
                            <th>اسم الخدمة</th>
                            <th>الاجمالي</th>
                            <th>حالة الدفع</th>
                            <th>العمليات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->id }}</td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td>{{ optional($invoice->Doctor)->name ?? '—' }}</td>
                                <td>{{ optional($invoice->Service)->name ?? optional($invoice->Group)->name ?? '—' }}</td>
                                <td>{{ number_format($invoice->total_with_tax, 2) }}</td>
                                <td>
                                    @php
                                        $ps = $invoice->payment_status ?? 'unpaid';
                                        $badge = $ps === 'paid' ? 'success' : ($ps === 'pending_review' ? 'warning' : ($ps === 'rejected' ? 'danger' : 'secondary'));
                                    @endphp
                                    <span class="badge badge-{{ $badge }}">
                                        {{ \App\Models\Invoice::$paymentStatusLabels[$ps] ?? $ps }}
                                    </span>
                                </td>
                                <td>
                                    @if($invoice->canPayViaShamCash())
                                        <a href="{{ route('patient.sham-cash.show', $invoice) }}" class="btn btn-sm btn-success">دفع شام كاش</a>
                                    @elseif($ps === 'pending_review')
                                        <a href="{{ route('patient.sham-cash.show', $invoice) }}" class="btn btn-sm btn-outline-warning">عرض الحالة</a>
                                    @elseif($ps === 'paid')
                                        <span class="text-success small">✓ مدفوعة</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
    <script src="{{URL::asset('dashboard/plugins/notify/js/notifIt.js')}}"></script>
@endsection

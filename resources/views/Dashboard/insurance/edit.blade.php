@extends('Dashboard.layouts.master')
@section('css')

    <!--Internal   Notify -->
    <link href="{{ URL::asset('Admin/assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet"/>
@endsection
@section('title')
    {{trans('insurance.edit_Insurance')}}
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{trans('main-sidebar_trans.Services')}}</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{trans('insurance.Insurance')}}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col">
            <div class="card hms-form-card">
                <div class="card-header">
                    <h5 class="mb-0 hms-form-title">تعديل شركة التأمين</h5>
                </div>
                <div class="card-body">

                    <form action="{{route('insurance.update','test')}}" method="post" class="hms-form">
                        @method('PUT')
                        @csrf

                        {{-- input hidden value => id   --}}
                        <input type="hidden" name="id" value="{{$insurances->id}}">

                        <div class="row">

                            <div class="col">
                                <label>{{trans('insurance.Company_code')}}</label>
                                <input type="text" name="insurance_code" value="{{$insurances->insurance_code}}"
                                       class="form-control @error('insurance_code') is-invalid @enderror">
                                @error('insurance_code')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label>{{trans('insurance.Company_name')}}</label>
                                <input type="text" name="name" value="{{$insurances->name}}"
                                       class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <br>

                        @include('Dashboard.insurance.partials.share-hints')

                        <div class="row">

                            <div class="col">
                                <label>{{trans('insurance.discount_percentage')}} % <small class="text-muted">(تحمل المريض)</small></label>
                                <input type="number" name="discount_percentage" min="0" max="100" step="0.01"
                                       value="{{$insurances->discount_percentage}}"
                                       class="form-control js-patient-share-input @error ('discount_percentage') is-invalid @enderror">
                                <small class="form-text text-muted">النسبة التي يتحملها المريض — تُكمل تلقائياً مع نسبة الشركة إلى 100%.</small>
                                @error('discount_percentage')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col">
                                <label>{{trans('insurance.Insurance_bearing_percentage')}} % <small class="text-muted">(تحمل الشركة)</small></label>
                                <input type="number" name="Company_rate" min="0" max="100" step="0.01"
                                    value="{{$insurances->Company_rate}}"  class="form-control js-company-share-input @error ('Company_rate') is-invalid @enderror">
                                <small class="form-text text-muted">النسبة التي تتحملها شركة التأمين — تُكمل تلقائياً مع نسبة المريض إلى 100%.</small>
                                @error('Company_rate')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <br>

                        <div class="row">
                            <div class="col">
                                <label>{{trans('insurance.notes')}}</label>
                                <textarea rows="5" cols="10" class="form-control"
                                          name="notes">{{$insurances->notes}}</textarea>
                            </div>
                        </div>

                        <br>

                        <div class="row">
                            <div class="col">
                                <label>حالة التفعيل</label>
                                 &nbsp;
                                <input name="status" {{$insurances->status == 1 ? 'checked' : ''}} value="1" type="checkbox" class="form-check-input" id="exampleCheck1">
                            </div>
                        </div>

                        <br>

                        <div class="hms-form-actions">
                            <button type="submit" class="btn btn-primary btn-hms-primary">{{trans('insurance.save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
@endsection
@section('js')
    <!--Internal  Notify js -->
    <script src="{{URL::asset('Admin/assets/plugins/notify/js/notifIt.js')}}"></script>
    <script src="{{URL::asset('Admin/assets/plugins/notify/js/notifit-custom.js')}}"></script>
    @include('Dashboard.insurance.partials.share-script')
@endsection

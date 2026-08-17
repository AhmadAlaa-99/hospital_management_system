@extends('Dashboard.layouts.master')
@section('css')
    <link href="{{URL::asset('Dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
@endsection
@section('title')
    {{ app()->getLocale()==='ar' ? 'تعديل طبيب' : 'Edit doctor' }}
@stop
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{trans('main-sidebar_trans.doctors')}}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ app()->getLocale()==='ar' ? 'تعديل' : 'Edit' }}</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('Dashboard.messages_alert')
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card hms-form-card">
                <div class="card-header">
                    <h5 class="mb-0 hms-form-title">{{ app()->getLocale()==='ar' ? 'تعديل طبيب' : 'Edit doctor' }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('Doctors.update', $doctor->id) }}" method="post" autocomplete="off" enctype="multipart/form-data" class="hms-form">
                        {{ method_field('patch') }}
                        {{ csrf_field() }}
                        <div class="hms-form-box">
                            <div class="mb-4 text-center">
                                @if($doctor->image)
                                    <img class="hms-avatar" style="width:120px;height:120px;"
                                         src="{{Url::asset('Dashboard/img/doctors/'.$doctor->image->filename)}}"
                                         alt="{{ $doctor->name }}">
                                @else
                                    <img class="hms-avatar" style="width:120px;height:120px;"
                                         src="{{Url::asset('Dashboard/img/doctor_default.png')}}"
                                         alt="default">
                                @endif
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-3"><label>{{trans('doctors.name')}}</label></div>
                                <div class="col-md-9"><input class="form-control" name="name" value="{{$doctor->name}}" type="text" required></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3"><label>{{trans('doctors.email')}}</label></div>
                                <div class="col-md-9"><input class="form-control" value="{{$doctor->email}}" name="email" type="email" required></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3"><label>{{ trans('doctors.phone') }}</label></div>
                                <div class="col-md-9">
                                    <input class="form-control" value="{{$doctor->phone}}" name="phone" type="tel" required>
                                    <input value="{{$doctor->id}}" name="id" type="hidden">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3"><label>{{trans('doctors.section')}}</label></div>
                                <div class="col-md-9">
                                    <select name="section_id" class="form-control" required>
                                        @foreach($sections as $section)
                                            <option value="{{$section->id}}" {{$section->id == $doctor->section_id ? 'selected':'' }}>{{$section->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3"><label>{{ trans('doctors.doctor_photo') }}</label></div>
                                <div class="col-md-9">
                                    <input type="file" accept="image/*" name="photo" onchange="loadFile(event)">
                                    <div class="mt-3">
                                        <img class="hms-avatar" style="width:120px;height:120px;" id="output" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="hms-form-actions">
                                <button type="submit" class="btn btn-primary btn-hms-primary pd-x-30">{{ trans('doctors.submit') }}</button>
                                <a href="{{ route('Doctors.index') }}" class="btn btn-secondary pd-x-30">{{ app()->getLocale()==='ar' ? 'رجوع' : 'Back' }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        var loadFile = function (event) {
            var output = document.getElementById('output');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function () {
                URL.revokeObjectURL(output.src)
            }
        };
    </script>
    <script src="{{URL::asset('Dashboard/plugins/notify/js/notifIt.js')}}"></script>
@endsection

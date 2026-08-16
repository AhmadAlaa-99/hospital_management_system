@extends('Dashboard.layouts.master')
@section('title')
    {{trans('main-sidebar_trans.Single_service')}}
@stop
@section('css')
    <link href="{{URL::asset('dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{trans('main-sidebar_trans.Services')}}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{trans('main-sidebar_trans.Single_service')}}</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('Dashboard.messages_alert')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card hms-table-card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-primary btn-hms-primary" data-toggle="modal" data-target="#add">
                            {{trans('Services.add_Service')}}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap hms-table" id="hms-services-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{trans('Services.name')}}</th>
                                <th>{{trans('Services.price')}}</th>
                                <th>{{trans('doctors.Status')}}</th>
                                <th>{{trans('Services.description')}}</th>
                                <th>{{trans('sections_trans.created_at')}}</th>
                                <th class="text-center">{{trans('sections_trans.Processes')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$service->name}}</td>
                                    <td>{{$service->price}}</td>
                                    <td>
                                        <span class="hms-badge {{ $service->status == 1 ? 'hms-badge-success' : 'hms-badge-danger' }}">
                                            <span class="dot-label bg-{{ $service->status == 1 ? 'success' : 'danger' }} ml-1"></span>
                                            {{ $service->status == 1 ? trans('doctors.Enabled') : trans('doctors.Not_enabled') }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($service->description, 50) }}</td>
                                    <td>{{ $service->created_at->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <div class="hms-actions">
                                            <button type="button"
                                                    class="hms-action-btn hms-action-btn--edit"
                                                    data-toggle="modal"
                                                    data-target="#edit{{$service->id}}"
                                                    title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                    class="hms-action-btn hms-action-btn--delete"
                                                    data-toggle="modal"
                                                    data-target="#delete{{$service->id}}"
                                                    title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @foreach($services as $service)
            @include('Dashboard.Services.Single Service.edit')
            @include('Dashboard.Services.Single Service.delete')
        @endforeach
        @include('Dashboard.Services.Single Service.add')
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('dashboard/plugins/notify/js/notifIt.js')}}"></script>
    <script src="{{URL::asset('/plugins/notify/js/notifit-custom.js')}}"></script>
    <script>
        $(function () {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#hms-services-table')) {
                $('#hms-services-table').DataTable({
                    responsive: false,
                    autoWidth: false,
                    order: [[0, 'asc']],
                    columnDefs: [
                        { orderable: false, targets: -1 },
                        { className: 'text-center', targets: -1 }
                    ],
                    language: {
                        searchPlaceholder: 'بحث...',
                        sSearch: '',
                        lengthMenu: '_MENU_',
                    }
                });
            }
        });
    </script>
@endsection

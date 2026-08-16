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
                <h4 class="content-title mb-0 my-auto">{{trans('Dashboard/main-sidebar_trans.sections')}}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{trans('Dashboard/main-sidebar_trans.view_all')}}</span>
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
                            {{trans('Dashboard/sections_trans.add_sections')}}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap hms-table" id="hms-sections-table">
                            <thead>
                            <tr>
                                <th class="wd-5p border-bottom-0">#</th>
                                <th class="border-bottom-0">{{trans('sections_trans.name_sections')}}</th>
                                <th class="border-bottom-0">{{trans('sections_trans.description')}}</th>
                                <th class="border-bottom-0">{{trans('sections_trans.created_at')}}</th>
                                <th class="border-bottom-0 text-center">{{trans('sections_trans.Processes')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sections as $section)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>
                                        <a href="{{route('Sections.show',$section->id)}}" class="font-weight-semibold">{{$section->name}}</a>
                                    </td>
                                    <td>{{ \Str::limit($section->description, 50) }}</td>
                                    <td>{{ $section->created_at->diffForHumans() }}</td>
                                    <td class="text-center">
                                        <div class="hms-actions">
                                            <button type="button" class="hms-action-btn hms-action-btn--edit" data-toggle="modal" data-target="#edit{{$section->id}}" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="hms-action-btn hms-action-btn--delete" data-toggle="modal" data-target="#delete{{$section->id}}" title="حذف">
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

        @foreach($sections as $section)
            @include('Dashboard.Sections.edit')
            @include('Dashboard.Sections.delete')
        @endforeach
        @include('Dashboard.Sections.add')
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('dashboard/plugins/notify/js/notifIt.js')}}"></script>
    <script src="{{URL::asset('/plugins/notify/js/notifit-custom.js')}}"></script>
    <script>
        $(function () {
            if ($.fn.DataTable && !$.fn.DataTable.isDataTable('#hms-sections-table')) {
                $('#hms-sections-table').DataTable({
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

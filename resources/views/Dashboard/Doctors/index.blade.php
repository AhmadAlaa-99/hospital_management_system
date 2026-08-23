@extends('Dashboard.layouts.master')
@section('title')
    {{trans('main-sidebar_trans.doctors')}}
@stop
@section('css')
    <link href="{{URL::asset('dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{trans('main-sidebar_trans.doctors')}}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{trans('main-sidebar_trans.view_all')}}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('Dashboard.messages_alert')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card mg-b-20 hms-table-card">
                <div class="card-header pb-0 d-flex flex-wrap" style="gap:8px">
                    <a href="{{route('Doctors.create')}}" class="btn btn-primary btn-hms-primary" role="button">
                        {{trans('doctors.add_doctor')}}
                    </a>
                    <a href="{{ route('export.table', ['type' => 'doctors']) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-export ml-1"></i> تصدير
                    </a>
                    <button type="button" class="btn btn-danger" id="btn_delete_all">
                        {{trans('doctors.delete_select')}}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive hms-table-scroll-x">
                        <table id="hms-doctors-table" class="table key-buttons text-md-nowrap hms-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>رقم السجل</th>
                                <th><input name="select_all" id="example-select-all" type="checkbox"/></th>
                                <th>{{trans('doctors.name')}}</th>
                                <th>{{trans('doctors.img')}}</th>
                                <th>{{trans('doctors.email')}}</th>
                                <th>{{trans('doctors.section')}}</th>
                                <th>{{trans('doctors.phone')}}</th>
                                <th>{{trans('doctors.appointments')}}</th>
                                <th>{{trans('doctors.Status')}}</th>
                                <th>{{trans('doctors.Processes')}}</th>
                                <th>{{trans('doctors.created_at')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($doctors as $doctor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $doctor->id }}</td>
                                    <td>
                                        <input type="checkbox" name="delete_select" value="{{$doctor->id}}" class="delete_select">
                                    </td>
                                    <td class="font-weight-semibold">{{ $doctor->name }}</td>
                                    <td>
                                        @if($doctor->image)
                                            <img class="hms-avatar" src="{{Url::asset('Dashboard/img/doctors/'.$doctor->image->filename)}}" alt="{{ $doctor->name }}">
                                        @else
                                            <img class="hms-avatar" src="{{Url::asset('Dashboard/img/doctor_default.png')}}" alt="default">
                                        @endif
                                    </td>
                                    <td>{{ $doctor->email }}</td>
                                    <td>{{ optional($doctor->section)->name }}</td>
                                    <td>{{ $doctor->phone }}</td>
                                    <td>
                                        <span class="hms-badge hms-badge-info">{{ $doctor->bookings_count }}</span>
                                    </td>
                                    <td>
                                        @if($doctor->status == 1)
                                            <span class="hms-badge hms-badge-success">
                                                <span class="dot-label bg-success ml-1"></span>
                                                {{ trans('doctors.Enabled') }}
                                            </span>
                                        @else
                                            <span class="hms-badge hms-badge-danger">
                                                <span class="dot-label bg-danger ml-1"></span>
                                                {{ trans('doctors.Not_enabled') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hms-actions">
                                            <a href="{{ route('Doctors.edit', $doctor->id) }}" class="hms-action-btn hms-action-btn--edit" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="hms-action-btn hms-action-btn--view" title="تغيير كلمة المرور" data-toggle="modal" data-target="#update_password{{ $doctor->id }}">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            @if($doctor->status == 1)
                                                <button type="button" class="hms-action-btn" style="background:#f59e0b!important" title="إلغاء التفعيل" data-toggle="modal" data-target="#update_status{{ $doctor->id }}">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                            @else
                                                <button type="button" class="hms-action-btn hms-action-btn--print" title="تفعيل" data-toggle="modal" data-target="#update_status{{ $doctor->id }}">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="hms-action-btn hms-action-btn--delete" title="حذف" data-toggle="modal" data-target="#delete{{ $doctor->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>{{ $doctor->created_at->diffForHumans() }}</td>
                                </tr>
                                @include('Dashboard.Doctors.delete')
                                @include('Dashboard.Doctors.delete_select')
                                @include('Dashboard.Doctors.update_password')
                                @include('Dashboard.Doctors.update_status')
                            @endforeach
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
    <script src="{{URL::asset('/plugins/notify/js/notifit-custom.js')}}"></script>
    <script>
        $(function() {
            jQuery("[name=select_all]").click(function(source) {
                checkboxes = jQuery("[name=delete_select]");
                for(var i in checkboxes){
                    checkboxes[i].checked = source.target.checked;
                }
            });

            if ($.fn.DataTable && $('#hms-doctors-table').length && !$.fn.DataTable.isDataTable('#hms-doctors-table')) {
                $('#hms-doctors-table').DataTable({
                    lengthChange: false,
                    scrollX: true,
                    autoWidth: false,
                    responsive: false,
                    buttons: ['copy', 'excel', 'pdf', 'colvis'],
                    language: {
                        searchPlaceholder: 'بحث...',
                        sSearch: '',
                        lengthMenu: 'عرض _MENU_',
                        info: 'عرض _START_ إلى _END_ من _TOTAL_',
                        paginate: { next: 'التالي', previous: 'السابق' },
                        buttons: { copy: 'نسخ', colvis: 'الأعمدة', print: 'طباعة', excel: 'Excel', pdf: 'PDF' }
                    },
                    columnDefs: [{ orderable: false, targets: [1, -2] }]
                }).buttons().container().appendTo('#hms-doctors-table_wrapper .col-md-6:eq(0)');
            }
        });
        $(function () {
            $("#btn_delete_all").click(function () {
                var selected = [];
                $("#hms-doctors-table input[name=delete_select]:checked").each(function () {
                    selected.push(this.value);
                });
                if (selected.length > 0) {
                    $('#delete_select').modal('show')
                    $('input[id="delete_select_id"]').val(selected);
                }
            });
        });
    </script>
@endsection

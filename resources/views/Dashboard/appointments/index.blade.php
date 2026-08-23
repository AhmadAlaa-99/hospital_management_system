@extends('Dashboard.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('Dashboard/plugins/sumoselect/sumoselect-rtl.css') }}">
    <link href="{{URL::asset('Dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('Dashboard/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('Dashboard/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css')}}" rel="stylesheet">
    <link href="{{URL::asset('Dashboard/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.css')}}" rel="stylesheet">
    <link href="{{URL::asset('Dashboard/plugins/pickerjs/picker.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('Dashboard/plugins/spectrum-colorpicker/spectrum.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المواعيد</h4><span
                    class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة المواعيد</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    @include('Dashboard.messages_alert')
    <!-- row opened -->
    <div class="row row-sm">
        <!--div-->
        <div class="col-xl-12">
            <div class="card hms-table-card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap hms-table" id="example1">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم المريض</th>
                                <th>البريد الالكتروني</th>
                                <th>القسم</th>
                                <th>الدكتور</th>
                                <th>الهاتف</th>
                                <th>التاريخ المفضل</th>
                                <th>الوقت المفضل</th>
                                <th>نوع الاستشارة</th>
                                <th>ملاحظات</th>
                                <th>العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($appointments as $appointment)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$appointment->name}}</a></td>
                                    <td>{{$appointment->email}}</td>
                                    <td>{{$appointment->section->name}}</td>
                                    <td>{{$appointment->doctor->name}}</td>
                                    <td>{{$appointment->phone}}</td>
                                    <td>{{ $appointment->preferred_date ? \Carbon\Carbon::parse($appointment->preferred_date)->format('Y-m-d') : '—' }}</td>
                                    <td>{{ $appointment->preferred_time ? substr((string) $appointment->preferred_time, 0, 5) : '—' }}</td>
                                    <td>
                                        @if($appointment->consultation_type === 'telemedicine')
                                            <span class="badge badge-info">عن بُعد</span>
                                        @else
                                            <span class="badge badge-secondary">حضوري</span>
                                        @endif
                                    </td>
                                    <td>{{$appointment->notes}}</td>
                                    <td>
                                        <button class="btn btn-sm btn-success" data-toggle="modal"
                                                data-target="#approval{{$appointment->id}}"><i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-toggle="modal"
                                                data-target="#Refusal{{$appointment->id}}"><i class="fas fa-remove-format"></i>
                                        </button>
                                    </td>
                                </tr>
                                 @include('Dashboard.appointments.approval')
                                 @include('Dashboard.appointments.refusal')
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div><!-- bd -->
            </div><!-- bd -->
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!--Internal  Form-elements js-->
    <script src="{{ URL::asset('Dashboard/js/select2.js') }}"></script>
    <script src="{{ URL::asset('Dashboard/js/advanced-form-elements.js') }}"></script>

    <!--Internal Sumoselect js-->
    <script src="{{ URL::asset('Dashboard/plugins/sumoselect/jquery.sumoselect.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{URL::asset('Dashboard/plugins/notify/js/notifIt.js')}}"></script>
    <script src="{{URL::asset('Dashboard/plugins/notify/js/notifit-custom.js')}}"></script>


    <!--Internal  Datepicker js -->
    <script src="{{URL::asset('Dashboard/plugins/jquery-ui/ui/widgets/datepicker.js')}}"></script>
    <!--Internal  jquery.maskedinput js -->
    <script src="{{URL::asset('Dashboard/plugins/jquery.maskedinput/jquery.maskedinput.js')}}"></script>
    <!--Internal  spectrum-colorpicker js -->
    <script src="{{URL::asset('Dashboard/plugins/spectrum-colorpicker/spectrum.js')}}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{URL::asset('Dashboard/plugins/select2/js/select2.min.js')}}"></script>
    <!--Internal Ion.rangeSlider.min js -->
    <script src="{{URL::asset('Dashboard/plugins/ion-rangeslider/js/ion.rangeSlider.min.js')}}"></script>
    <!--Internal  jquery-simple-datetimepicker js -->
    <script src="{{URL::asset('Dashboard/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js')}}"></script>
    <!-- Ionicons js -->
    <script src="{{URL::asset('Dashboard/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js')}}"></script>
    <!--Internal  pickerjs js -->
    <script src="{{URL::asset('Dashboard/plugins/pickerjs/picker.min.js')}}"></script>
    <!-- Internal form-elements js -->
    <script src="{{URL::asset('Dashboard/js/form-elements.js')}}"></script>
    <script>
        $(document).on('shown.bs.modal', '[id^="approval"]', function () {
            var $input = $(this).find('.datetimepicker-approval');
            if (!$input.length || $input.data('datetimepicker')) {
                return;
            }
            $input.datetimepicker({
                format: 'yyyy-mm-dd hh:ii',
                autoclose: true,
                todayBtn: true,
                minuteStep: 15
            });
        });
    </script>
@endsection

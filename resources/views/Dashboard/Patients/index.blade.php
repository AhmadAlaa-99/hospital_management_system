@extends('Dashboard.layouts.master')
@section('css')
    <link href="{{URL::asset('dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">المرضي</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ قائمة المرضي</span>
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
							<div class="card-header pb-0">
								<div class="d-flex justify-content-between">
                                    <a href="{{route('Patients.create')}}" class="btn btn-primary btn-hms-primary">اضافة مريض جديد</a>
								</div>
							</div>
							<div class="card-body">
								<div class="table-responsive">
									<table class="table text-md-nowrap hms-table" id="example1">
										<thead>
											<tr>
												<th>#</th>
												<th>رقم السجل</th>
												<th>اسم المريض</th>
												<th >البريد الالكتروني</th>
												<th>تاريخ الميلاد</th>
												<th>رقم الهاتف</th>
												<th>الجنس</th>
                                                <th >فصلية الدم</th>
                                                <th >العنوان</th>
                                                <th>العمليات</th>
											</tr>
										</thead>
										<tbody>
                                        @foreach($Patients as $Patient)
											<tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$Patient->id}}</td>
                                                <td><a href="{{route('Patients.show',$Patient->id)}}">{{$Patient->name}}</a></td>
                                                <td>{{$Patient->email}}</td>
                                                <td>{{$Patient->Date_Birth}}</td>
                                                <td>{{$Patient->Phone}}</td>
                                                <td>
                                                    @if($Patient->Gender == 1)
                                                        <span class="hms-badge hms-badge-info">ذكر</span>
                                                    @else
                                                        <span class="hms-badge hms-badge-success">أنثى</span>
                                                    @endif
                                                </td>
                                                <td>{{$Patient->Blood_Group}}</td>
                                                <td>{{$Patient->Address}}</td>
                                                <td>
                                                    <div class="hms-actions">
                                                    <a href="{{route('Patients.edit',$Patient->id)}}" class="hms-action-btn hms-action-btn--edit" title="تعديل"><i class="fas fa-edit"></i></a>
                                                    <button type="button" class="hms-action-btn hms-action-btn--edit" style="background:#f59e0b;border-color:#f59e0b;color:#fff" data-toggle="modal" data-target="#ResetPassword{{$Patient->id}}" title="إعادة تعيين كلمة المرور"><i class="fas fa-key"></i></button>
                                                    <button type="button" class="hms-action-btn hms-action-btn--delete" data-toggle="modal" data-target="#Deleted{{$Patient->id}}" title="حذف"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                    <a href="{{route('Patients.show',$Patient->id)}}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>

                                                </td>
											</tr>
                                           @include('Dashboard.Patients.Deleted')
                                           @include('Dashboard.Patients.ResetPassword')
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
    <!--Internal  Notify js -->
    <script src="{{URL::asset('dashboard/plugins/notify/js/notifIt.js')}}"></script>
    <script src="{{URL::asset('/plugins/notify/js/notifit-custom.js')}}"></script>
    <script>
        function generatePatientPassword(patientId) {
            var chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
            var password = '';
            for (var i = 0; i < 10; i++) {
                password += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('new-password-' + patientId).value = password;
        }
    </script>
@endsection

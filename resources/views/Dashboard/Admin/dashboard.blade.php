@extends('Dashboard.layouts.master')
@section('css')
<link href="{{URL::asset('Dashboard/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة تحكم المدير</h2>
						</div>
					</div>
					<div class="main-dashboard-header-right">
						<div>
							<label class="tx-13">الخدمات المفردة</label>
							<h5>{{ \App\Models\Service::count() }}</h5>
						</div>
						<div>
							<label class="tx-13">الخدمات المجمعة</label>
							<h5>{{ \App\Models\Group::count() }}</h5>
						</div>
						<div>
							<label class="tx-13">الفواتير</label>
							<h5>{{ \App\Models\Invoice::count() }}</h5>
						</div>
					</div>
				</div>
@endsection
@section('content')
				@php
					$pendingAppointments = \App\Models\Appointment::where('type', 'غير مؤكد')->count();
					$confirmedAppointments = \App\Models\Appointment::where('type', 'مؤكد')->count();
					$finishedAppointments = \App\Models\Appointment::where('type', 'منتهي')->count();
					$recentAppointments = \App\Models\Appointment::with(['doctor', 'section'])->latest()->take(8)->get();
					$recentDoctors = \App\Models\Doctor::with('section')->latest()->take(5)->get();
				@endphp

				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div>
									<h6 class="mb-3 tx-12 text-white">عدد الأطباء</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ \App\Models\Doctor::count() }}</h4>
										</div>
									</div>
								</div>
							</div>
							<span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-danger-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div>
									<h6 class="mb-3 tx-12 text-white">عدد المرضى</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ \App\Models\Patient::count() }}</h4>
										</div>
									</div>
								</div>
							</div>
							<span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-success-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div>
									<h6 class="mb-3 tx-12 text-white">عدد الأقسام</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ \App\Models\Section::count() }}</h4>
										</div>
									</div>
								</div>
							</div>
							<span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-warning-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div>
									<h6 class="mb-3 tx-12 text-white">مواعيد بانتظار التأكيد</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $pendingAppointments }}</h4>
										</div>
									</div>
								</div>
							</div>
							<span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
				</div>

				<div class="row row-sm">
					<div class="col-xl-4 col-lg-6 col-md-6">
						<div class="card hms-table-card">
							<div class="card-body">
								<h5 class="card-title">المواعيد المؤكدة</h5>
								<h2 class="mb-0 text-success">{{ $confirmedAppointments }}</h2>
								<a href="{{ route('appointments.index2') }}" class="tx-12">عرض القائمة</a>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-lg-6 col-md-6">
						<div class="card hms-table-card">
							<div class="card-body">
								<h5 class="card-title">المواعيد المنتهية</h5>
								<h2 class="mb-0 text-muted">{{ $finishedAppointments }}</h2>
								<a href="{{ route('appointments.index3') }}" class="tx-12">عرض القائمة</a>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-lg-6 col-md-6">
						<div class="card hms-table-card">
							<div class="card-body">
								<h5 class="card-title">طلبات الحجز الجديدة</h5>
								<h2 class="mb-0 text-danger">{{ $pendingAppointments }}</h2>
								<a href="{{ route('appointments.index') }}" class="tx-12">مراجعة الطلبات</a>
							</div>
						</div>
					</div>
				</div>

				<div class="row row-sm row-deck">
					<div class="col-md-12 col-lg-8 col-xl-8">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">أحدث طلبات المواعيد</h4>
							</div>
							<span class="tx-12 tx-muted mb-3">آخر الطلبات الواردة من الموقع</span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0">
									<thead>
										<tr>
											<th>#</th>
											<th>المريض</th>
											<th>القسم</th>
											<th>الطبيب</th>
											<th>الحالة</th>
										</tr>
									</thead>
									<tbody>
										@forelse($recentAppointments as $appointment)
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $appointment->name }}</td>
												<td>{{ optional($appointment->section)->name }}</td>
												<td>{{ optional($appointment->doctor)->name }}</td>
												<td>
													@if($appointment->type === 'مؤكد')
														<span class="hms-badge hms-badge-success">مؤكد</span>
													@elseif($appointment->type === 'منتهي')
														<span class="badge badge-secondary">منتهي</span>
													@else
														<span class="hms-badge hms-badge-info">غير مؤكد</span>
													@endif
												</td>
											</tr>
										@empty
											<tr>
												<td colspan="5" class="text-center">لا توجد مواعيد حالياً</td>
											</tr>
										@endforelse
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card hms-table-card">
							<div class="card-header pb-1">
								<h3 class="card-title mb-2">أحدث الأطباء</h3>
							</div>
							<div class="card-body p-0 customers mt-1">
								<div class="list-group list-lg-group list-group-flush">
									@forelse($recentDoctors as $doctor)
										<div class="list-group-item list-group-item-action">
											<div class="media mt-0">
												<div class="media-body">
													<div class="d-flex align-items-center">
														<div class="mt-0">
															<h5 class="mb-1 tx-15">{{ $doctor->name }}</h5>
															<p class="mb-0 tx-13 text-muted">{{ optional($doctor->section)->name }}</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									@empty
										<div class="list-group-item">لا يوجد أطباء</div>
									@endforelse
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
@endsection
@section('js')
<script src="{{URL::asset('Dashboard/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<script src="{{URL::asset('Dashboard/plugins/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('Dashboard/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
<script src="{{URL::asset('Dashboard/js/dashboard.sampledata.js')}}"></script>
<script src="{{URL::asset('Dashboard/js/index.js')}}"></script>
@endsection

@extends('Dashboard.layouts.master')
@section('css')
<link href="{{URL::asset('Dashboard/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة تحكم الدكتور</h2>
						  <p class="mg-b-0">مرحباً {{ auth()->user()->name }}</p>
						</div>
					</div>
				</div>
@endsection
@section('content')
				@php
					$doctorId = auth()->user()->id;
					$totalInvoices = \App\Models\Invoice::where('doctor_id', $doctorId)->count();
					$pendingInvoices = \App\Models\Invoice::where('doctor_id', $doctorId)->where('invoice_status', 1)->count();
					$reviewInvoices = \App\Models\Invoice::where('doctor_id', $doctorId)->where('invoice_status', 2)->count();
					$completedInvoices = \App\Models\Invoice::where('doctor_id', $doctorId)->where('invoice_status', 3)->count();
					$recentInvoices = \App\Models\Invoice::with('patient')->where('doctor_id', $doctorId)->latest()->take(8)->get();
				@endphp

				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div>
									<h6 class="mb-3 tx-12 text-white">عدد الفواتير</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $totalInvoices }}</h4>
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
									<h6 class="mb-3 tx-12 text-white">فواتير تحت الاجراء</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $pendingInvoices }}</h4>
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
									<h6 class="mb-3 tx-12 text-white">فواتير مكتملة</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $completedInvoices }}</h4>
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
									<h6 class="mb-3 tx-12 text-white">فواتير المراجعات</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $reviewInvoices }}</h4>
										</div>
									</div>
								</div>
							</div>
							<span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
				</div>

				<div class="row row-sm row-deck">
					<div class="col-md-12">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">آخر الفواتير</h4>
							</div>
							<span class="tx-12 tx-muted mb-3">أحدث فواتير المرضى المرتبطة بحسابك</span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0">
									<thead>
										<tr>
											<th>#</th>
											<th>تاريخ الفاتورة</th>
											<th>المريض</th>
											<th>الحالة</th>
										</tr>
									</thead>
									<tbody>
										@forelse($recentInvoices as $invoice)
											<tr>
												<td>{{ $loop->iteration }}</td>
												<td>{{ $invoice->created_at }}</td>
												<td>{{ optional($invoice->patient)->name }}</td>
												<td>
													@if($invoice->invoice_status == 1)
														<span class="hms-badge hms-badge-danger">تحت الاجراء</span>
													@elseif($invoice->invoice_status == 2)
														<span class="hms-badge hms-badge-warning">مراجعة</span>
													@else
														<span class="hms-badge hms-badge-success">مكتملة</span>
													@endif
												</td>
											</tr>
										@empty
											<tr>
												<td colspan="4" class="text-center">لا توجد فواتير حالياً</td>
											</tr>
										@endforelse
									</tbody>
								</table>
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

@extends('Dashboard.layouts.master')
@section('css')
<link href="{{URL::asset('Dashboard/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
@endsection
@section('page-header')
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة تحكم المريض</h2><br>
						  <p class="mg-b-0">مرحبا بعودتك مرة أخرى {{ auth()->user()->name }}</p>
						</div>
					</div>
				</div>
@endsection
@section('content')
				@php
					$patientId = auth()->user()->id;
					$invoicesCount = \App\Models\Invoice::where('patient_id', $patientId)->count();
					$paymentsTotal = \App\Models\PatientAccount::where('patient_id', $patientId)->sum('credit');
					$recentInvoices = \App\Models\Invoice::with(['doctor', 'patient'])->where('patient_id', $patientId)->latest()->take(5)->get();
				@endphp

				<div class="row row-sm">
					<div class="col-xl-6 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div>
									<h6 class="mb-3 tx-12 text-white">اجمالي عدد الفواتير</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $invoicesCount }}</h4>
										</div>
									</div>
								</div>
							</div>
							<span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>

					<div class="col-xl-6 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-danger-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div>
									<h6 class="mb-3 tx-12 text-white">اجمالي المدفوعات</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div>
											<h4 class="tx-20 font-weight-bold mb-1 text-white">
												<a style="color: white" href="{{ route('payments.patient') }}">{{ $paymentsTotal }}</a>
											</h4>
										</div>
									</div>
								</div>
							</div>
							<span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
						</div>
					</div>
				</div>

                <div class="row row-sm row-deck">
                    <div class="col-md-12 col-lg-12 col-xl-12">
                        <div class="card card-table-two">
                            <div class="d-flex justify-content-between">
                                <h2 class="card-title mb-1">آخر 5 فواتير</h2>
                            </div><br>
                            <div class="table-responsive country-table">
                                <table class="table table-striped table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>تاريخ الفاتورة</th>
                                        <th>اسم المريض</th>
                                        <th>اسم الطبيب</th>
                                        <th>حالة الفاتورة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recentInvoices as $invoice)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $invoice->created_at }}</td>
                                            <td>{{ optional($invoice->patient)->name }}</td>
                                            <td>{{ optional($invoice->doctor)->name }}</td>
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
                                            <td colspan="5" class="text-center">لا توجد بيانات</td>
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

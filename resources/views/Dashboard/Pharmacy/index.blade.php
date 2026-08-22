@extends('Dashboard.layouts.master')
@section('title') صيدلية العيادة @endsection
@section('content')
@include('Dashboard.messages_alert')

<div class="row row-sm mb-3">
    <div class="col-md-3"><div class="card p-3 border-warning"><small>مخزون منخفض</small><h3 class="text-warning">{{ $lowStock->count() }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3 border-primary"><small>وصفات بانتظار الصرف</small><h3 class="text-primary">{{ $pendingDiagnostics->count() }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3 border-success"><small>فواتير اليوم</small><h3>{{ \App\Models\PharmacyInvoice::whereDate('created_at', today())->count() }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><small>أدوية في المخزون</small><h3>{{ \App\Models\Medicine::where('is_active', true)->count() }}</h3></div></div>
</div>

@if($lowStock->count())
<div class="alert alert-warning">
    <strong>تنبيه مخزون منخفض:</strong>
    @foreach($lowStock as $m)
        {{ $m->name }} ({{ $m->quantity }}/{{ $m->min_stock_level }})@if(!$loop->last)، @endif
    @endforeach
</div>
@endif

<div class="card hms-table-card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-prescription"></i> وصفات إلكترونية بانتظار الصرف</span>
        <span class="badge badge-primary">كشف → وصفة → صرف</span>
    </div>
    <div class="card-body">
        <table class="table hms-table table-sm">
            <thead><tr><th>المريض</th><th>الطبيب</th><th>التشخيص</th><th>أدوية</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
            @forelse($pendingDiagnostics as $dx)
                <tr>
                    <td>{{ optional($dx->patient)->name ?? '#'.$dx->patient_id }}</td>
                    <td>{{ optional($dx->Doctor)->name ?? '—' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($dx->diagnosis, 40) }}</td>
                    <td>{{ $dx->prescriptions->where('is_dispensed', false)->count() }} دواء</td>
                    <td>{{ $dx->date }}</td>
                    <td>
                        <a href="{{ route('pharmacy.dispense-prescription', $dx) }}" class="btn btn-sm btn-success">صرف الوصفة</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">لا توجد وصفات بانتظار الصرف</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card hms-table-card mb-3">
            <div class="card-header">إضافة دواء للمخزون</div>
            <div class="card-body">
                <form action="{{ route('pharmacy.medicines.store') }}" method="POST">@csrf
                    <div class="form-group"><input name="name" class="form-control form-control-sm" placeholder="اسم الدواء *" required></div>
                    <div class="form-group"><input name="generic_name" class="form-control form-control-sm" placeholder="الاسم العلمي"></div>
                    <div class="row">
                        <div class="col-6"><input type="number" name="quantity" class="form-control form-control-sm" placeholder="الكمية *" required></div>
                        <div class="col-6"><input type="number" step="0.01" name="unit_price" class="form-control form-control-sm" placeholder="السعر *" required></div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6"><input type="date" name="expiry_date" class="form-control form-control-sm"></div>
                        <div class="col-6"><input type="number" name="min_stock_level" class="form-control form-control-sm" value="10"></div>
                    </div>
                    <button class="btn btn-primary btn-sm btn-block mt-2">إضافة</button>
                </form>
                <hr>
                <a href="{{ route('pharmacy.dispense') }}" class="btn btn-outline-secondary btn-sm btn-block">صرف يدوي (بدون وصفة)</a>
                <a href="{{ route('pharmacy.invoices') }}" class="btn btn-outline-info btn-sm btn-block mt-1">كل فواتير الصيدلية</a>
            </div>
        </div>
        <div class="card hms-table-card">
            <div class="card-header">آخر الفواتير</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($recentInvoices as $inv)
                        <li class="list-group-item d-flex justify-content-between">
                            <a href="{{ route('pharmacy.invoices.show', $inv) }}">{{ $inv->invoice_number }}</a>
                            <span>{{ number_format($inv->total_amount, 0) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card hms-table-card">
            <div class="card-header">مخزون صيدلية العيادة (أدوية شائعة)</div>
            <div class="card-body">
                <table class="table hms-table table-sm">
                    <thead><tr><th>الدواء</th><th>علمي</th><th>الكمية</th><th>السعر</th><th>الانتهاء</th><th>الحالة</th></tr></thead>
                    <tbody>
                    @foreach($medicines as $med)
                        <tr class="{{ $med->isLowStock() ? 'table-warning' : '' }}">
                            <td>{{ $med->name }}</td>
                            <td>{{ $med->generic_name ?? '—' }}</td>
                            <td>
                                @if($med->isLowStock())
                                    <span class="text-danger font-weight-bold">{{ $med->quantity }}</span>
                                @else
                                    {{ $med->quantity }}
                                @endif
                            </td>
                            <td>{{ number_format($med->unit_price, 2) }}</td>
                            <td>{{ optional($med->expiry_date)->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                @if($med->isLowStock())
                                    <span class="badge badge-warning">منخفض</span>
                                @elseif(!$med->is_active)
                                    <span class="badge badge-secondary">موقوف</span>
                                @else
                                    <span class="badge badge-success">متوفر</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $medicines->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

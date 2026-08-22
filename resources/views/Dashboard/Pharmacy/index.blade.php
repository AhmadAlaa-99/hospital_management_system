@extends('Dashboard.layouts.master')
@section('title') صيدلية العيادة @endsection
@section('content')
@include('Dashboard.messages_alert')
@if($lowStock->count())
<div class="alert alert-warning">تنبيه مخزون منخفض: @foreach($lowStock as $m) {{ $m->name }} ({{ $m->quantity }}) @if(!$loop->last), @endif @endforeach</div>
@endif
<div class="row">
    <div class="col-lg-5">
        <div class="card hms-table-card mb-3"><div class="card-header">إضافة دواء</div><div class="card-body">
            <form action="{{ route('pharmacy.medicines.store') }}" method="POST">@csrf
                <div class="form-group"><input name="name" class="form-control" placeholder="اسم الدواء" required></div>
                <div class="form-group"><input name="generic_name" class="form-control" placeholder="الاسم العلمي"></div>
                <div class="row">
                    <div class="col-6"><input type="number" name="quantity" class="form-control" placeholder="الكمية" required></div>
                    <div class="col-6"><input type="number" step="0.01" name="unit_price" class="form-control" placeholder="السعر" required></div>
                </div>
                <div class="row mt-2">
                    <div class="col-6"><input type="date" name="expiry_date" class="form-control"></div>
                    <div class="col-6"><input type="number" name="min_stock_level" class="form-control" value="10" placeholder="حد التنبيه"></div>
                </div>
                <button class="btn btn-primary mt-3">حفظ</button>
            </form>
        </div></div>
        <a href="{{ route('pharmacy.dispense') }}" class="btn btn-success btn-block">صرف وصفة</a>
    </div>
    <div class="col-lg-7">
        <div class="card hms-table-card"><div class="card-header">المخزون</div><div class="card-body">
            <table class="table hms-table table-sm">
                <thead><tr><th>الدواء</th><th>الكمية</th><th>السعر</th><th>الانتهاء</th></tr></thead>
                <tbody>
                @foreach($medicines as $med)
                    <tr class="{{ $med->isLowStock() ? 'table-warning' : '' }}">
                        <td>{{ $med->name }}</td>
                        <td>{{ $med->quantity }}</td>
                        <td>{{ number_format($med->unit_price, 2) }}</td>
                        <td>{{ optional($med->expiry_date)->format('Y-m-d') ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $medicines->links() }}
        </div></div>
    </div>
</div>
@endsection

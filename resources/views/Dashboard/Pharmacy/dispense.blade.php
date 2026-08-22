@extends('Dashboard.layouts.master')
@section('title') صرف دواء @endsection
@section('content')
<div class="card hms-table-card">
    <div class="card-body">
        <form action="{{ route('pharmacy.dispense.store') }}" method="POST">@csrf
            <div class="form-group">
                <label>المريض</label>
                <select name="patient_id" class="form-control" required>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->name ?? ('#'.$p->id) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>الدواء</label>
                <select name="medicine_id" class="form-control" required>
                    @foreach($medicines as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} (متوفر: {{ $m->quantity }}) — {{ number_format($m->unit_price,2) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>الكمية</label>
                <input type="number" name="quantity_dispensed" class="form-control" min="1" value="1" required>
            </div>
            <button class="btn btn-primary">صرف</button>
            <a href="{{ route('pharmacy.index') }}" class="btn btn-secondary">رجوع</a>
        </form>
    </div>
</div>
@endsection

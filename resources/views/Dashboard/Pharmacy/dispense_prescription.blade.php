@extends('Dashboard.layouts.master')
@section('title') صرف الوصفة الإلكترونية @endsection
@section('content')
@include('Dashboard.messages_alert')

<div class="card hms-table-card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>المريض:</strong> {{ optional($diagnostic->patient)->name }}</p>
                <p><strong>الطبيب:</strong> {{ optional($diagnostic->Doctor)->name }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>التشخيص:</strong> {{ $diagnostic->diagnosis }}</p>
                <p><strong>التاريخ:</strong> {{ $diagnostic->date }}</p>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('pharmacy.dispense-prescription.store', $diagnostic) }}" method="POST">
    @csrf
    <div class="card hms-table-card mb-3">
        <div class="card-header bg-success text-white">e-Prescription — اختر الأدوية للصرف</div>
        <div class="card-body">
            <table class="table hms-table">
                <thead>
                <tr>
                    <th>✓</th>
                    <th>من الوصفة</th>
                    <th>الجرعة</th>
                    <th>مطابقة المخزون</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                </tr>
                </thead>
                <tbody>
                @foreach($lines as $index => $line)
                    @php
                        $rx = $line['prescription'];
                        $matched = $line['matched_medicine'];
                    @endphp
                    <tr class="{{ $matched ? '' : 'table-danger' }}">
                        <td>
                            <input type="checkbox" name="lines[{{ $index }}][enabled]" value="1" {{ $matched ? 'checked' : 'disabled' }}>
                            <input type="hidden" name="lines[{{ $index }}][prescription_id]" value="{{ $rx->id }}">
                        </td>
                        <td><strong>{{ $rx->medicine_name }}</strong></td>
                        <td>
                            {{ $rx->dosage ?? '—' }} — {{ $rx->frequency ?? '' }}
                            @if($rx->duration_days)<br><small>{{ $rx->duration_days }} يوم</small>@endif
                        </td>
                        <td>
                            @if($matched)
                                <select name="lines[{{ $index }}][medicine_id]" class="form-control form-control-sm">
                                    <option value="{{ $matched->id }}" selected>{{ $matched->name }} ({{ $matched->quantity }})</option>
                                    @foreach($allMedicines->where('id', '!=', $matched->id) as $alt)
                                        <option value="{{ $alt->id }}">{{ $alt->name }} ({{ $alt->quantity }})</option>
                                    @endforeach
                                </select>
                            @else
                                <select name="lines[{{ $index }}][medicine_id]" class="form-control form-control-sm" disabled>
                                    <option>— غير متوفر —</option>
                                </select>
                                <small class="text-danger">أضف الدواء للمخزون أولاً</small>
                            @endif
                        </td>
                        <td>
                            <input type="number" name="lines[{{ $index }}][quantity]" class="form-control form-control-sm" style="width:80px"
                                   value="{{ $line['suggested_qty'] }}" min="1" {{ $matched ? '' : 'disabled' }}>
                        </td>
                        <td>
                            @if($matched)
                                {{ number_format($matched->unit_price, 2) }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="form-group mt-3">
                <label>ملاحظات الفاتورة</label>
                <input type="text" name="notes" class="form-control" placeholder="اختياري">
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('تأكيد صرف الوصفة وإصدار فاتورة الصيدلية؟')">
        <i class="fas fa-pills"></i> صرف وإصدار فاتورة
    </button>
    <a href="{{ route('pharmacy.index') }}" class="btn btn-secondary">رجوع</a>
</form>
@endsection

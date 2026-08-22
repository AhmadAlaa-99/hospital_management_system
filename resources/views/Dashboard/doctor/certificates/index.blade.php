@extends('Dashboard.layouts.master')
@section('title') الشهادات الطبية @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card mb-3">
    <div class="card-body d-flex justify-content-between">
        <h5 class="mb-0">الشهادات الصادرة</h5>
        <a href="{{ route('doctor.certificates.create') }}" class="btn btn-primary btn-sm">شهادة جديدة</a>
    </div>
</div>
<div class="card hms-table-card">
    <div class="card-body">
        <table class="table hms-table">
            <thead><tr><th>المرجع</th><th>المريض</th><th>النوع</th><th>التاريخ</th><th></th></tr></thead>
            <tbody>
            @forelse($certificates as $cert)
                <tr>
                    <td>{{ $cert->reference_number }}</td>
                    <td>{{ optional($cert->patient)->name ?? '#'.$cert->patient_id }}</td>
                    <td>{{ \App\Models\MedicalCertificate::$typeLabels[$cert->type] ?? $cert->type }}</td>
                    <td>{{ $cert->issued_at->format('Y-m-d') }}</td>
                    <td><a href="{{ route('doctor.certificates.pdf', $cert) }}" class="btn btn-sm btn-success">PDF</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">لا توجد شهادات</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $certificates->links() }}
    </div>
</div>
@endsection

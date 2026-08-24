@extends('Dashboard.layouts.master')
@section('title') التقارير والإحصائيات @endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0">التقارير والإحصائيات</h4></div>
    <div class="d-flex" style="gap:8px">
        <a href="{{ route('export.reports') }}" class="btn btn-success btn-sm"><i class="fas fa-file-excel ml-1"></i> تصدير Excel</a>
        <a href="{{ route('export.reports.pdf') }}" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf ml-1"></i> تصدير PDF</a>
    </div>
</div>
@endsection
@section('content')
<div class="row row-sm mb-4">
    <div class="col-md-3"><div class="card p-3"><small>المرضى</small><h3>{{ $stats['total_patients'] }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><small>الإيرادات</small><h3>{{ number_format($stats['total_revenue'], 0) }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><small>مواعيد مؤكدة</small><h3>{{ $stats['confirmed_appointments'] }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><small>متوسط تقييم الأطباء</small><h3>{{ $stats['avg_doctor_rating'] }} / 5</h3></div></div>
</div>
<div class="row row-sm mb-4">
    <div class="col-md-3"><div class="card p-3"><small>مواعيد مرفوضة (No-Show)</small><h3>{{ $stats['no_show_count'] }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><small>استشارات عن بُعد</small><h3>{{ $stats['telemedicine_count'] }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><small>مواعيد إسعاف/طوارئ</small><h3>{{ $stats['emergency_appointments'] }}</h3></div></div>
</div>
<div class="row row-sm">
    <div class="col-lg-6"><div class="card"><div class="card-body"><h5>المرضى الجدد (6 أشهر)</h5><canvas id="patientsChart" height="200"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card"><div class="card-body"><h5>الإيرادات الشهرية</h5><canvas id="revenueChart" height="200"></canvas></div></div></div>
</div>
<div class="row row-sm mt-3">
    <div class="col-lg-12"><div class="card"><div class="card-body"><h5>أداء الأقسام</h5><canvas id="sectionsChart" height="120"></canvas></div></div></div>
</div>
<div class="row row-sm mt-3">
    <div class="col-lg-12">
        <div class="card hms-table-card"><div class="card-body">
            <table class="table table-bordered hms-table">
                <thead><tr><th>القسم</th><th>الأطباء</th><th>المواعيد</th><th>الفواتير</th><th>الإيرادات</th></tr></thead>
                <tbody>
                @foreach($sectionPerformance as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td><td>{{ $row['doctors'] }}</td><td>{{ $row['appointments'] }}</td>
                        <td>{{ $row['invoices'] }}</td><td>{{ number_format($row['revenue'], 0) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div></div>
    </div>
</div>
<div class="row row-sm mt-3">
    <div class="col-lg-6">
        <div class="card hms-table-card"><div class="card-body">
            <h5>أكثر التشخيصات</h5>
            <table class="table table-sm">
                @foreach($topDiagnoses as $d)
                    <tr><td>{{ \Illuminate\Support\Str::limit($d->diagnosis, 50) }}</td><td>{{ $d->total }}</td></tr>
                @endforeach
            </table>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card hms-table-card"><div class="card-body">
            <h5>متوسط انتظار العيادات (دقيقة)</h5>
            <table class="table table-sm">
                @foreach($sectionWaitStats as $s)
                    <tr><td>{{ $s['name'] }}</td><td>{{ $s['avg_wait_minutes'] }}</td></tr>
                @endforeach
            </table>
        </div></div>
    </div>
</div>
@endsection
@section('js')
<script src="{{ URL::asset('Dashboard/plugins/chart.js/Chart.bundle.min.js') }}"></script>
<script>
const months = @json($months->values());
const patientsData = @json(array_values($patientsByMonth->toArray()));
const revenueData = @json(array_values($revenueByMonth->toArray()));
const sectionLabels = @json($sectionPerformance->pluck('name'));
const sectionRevenue = @json($sectionPerformance->pluck('revenue'));

new Chart(document.getElementById('patientsChart'), {
    type: 'line',
    data: { labels: months, datasets: [{ label: 'مرضى', data: patientsData, borderColor: '#4e73df', fill: false }] }
});
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: { labels: months, datasets: [{ label: 'إيرادات', data: revenueData, backgroundColor: '#1cc88a' }] }
});
new Chart(document.getElementById('sectionsChart'), {
    type: 'bar',
    data: { labels: sectionLabels, datasets: [{ label: 'إيرادات القسم', data: sectionRevenue, backgroundColor: '#36b9cc' }] }
});
</script>
@endsection

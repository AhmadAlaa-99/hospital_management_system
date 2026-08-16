@extends('Dashboard.layouts.master')
@section('title') طلبات الإسعاف @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card mb-3">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <strong>سيارات متاحة للإرسال:</strong>
                <span class="badge badge-success">{{ $availableAmbulances->count() }}</span>
            </div>
            @if($availableAmbulances->isEmpty())
                <small class="text-danger">جميع السيارات مشغولة بطلبات غير مكتملة أو غير متاحة حالياً.</small>
            @else
                <small class="text-muted">تظهر فقط السيارات غير المرتبطة بطلب قيد التنفيذ.</small>
            @endif
        </div>
    </div>
</div>
<div class="card hms-table-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table hms-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الهاتف</th>
                    <th>العنوان</th>
                    <th>الحالة</th>
                    <th>السيارة</th>
                    <th>العمليات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>{{ $req->patient_name }}</td>
                        <td>{{ $req->phone }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($req->address, 40) }}</td>
                        <td>
                            <span class="badge hms-badge-status hms-badge-status--{{ $req->status }}">
                                {{ \App\Models\AmbulanceRequest::$statusLabels[$req->status] ?? $req->status }}
                            </span>
                        </td>
                        <td>{{ optional($req->ambulance)->car_number ?? '—' }}</td>
                        <td>
                            @if($req->status === 'pending')
                                @if($availableAmbulances->isEmpty())
                                    <span class="text-muted small">لا توجد سيارة متاحة</span>
                                @else
                                    <form action="{{ route('ambulance-requests.dispatch', $req) }}" method="POST" class="d-inline-flex flex-wrap align-items-center gap-1">
                                        @csrf
                                        <select name="ambulance_id" class="form-control form-control-sm" style="min-width:120px" required>
                                            <option value="">— اختر سيارة —</option>
                                            @foreach($availableAmbulances as $a)
                                                <option value="{{ $a->id }}">{{ $a->car_number }} — {{ $a->car_model }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-success">إرسال</button>
                                    </form>
                                @endif
                            @endif
                            @if($req->status === 'dispatched')
                                <form action="{{ route('ambulance-requests.complete', $req) }}" method="POST" class="d-inline">@csrf
                                    <button type="submit" class="btn btn-sm btn-primary">إكمال</button>
                                </form>
                            @endif
                            @if(in_array($req->status, ['pending','dispatched']))
                                <form action="{{ route('ambulance-requests.cancel', $req) }}" method="POST" class="d-inline">@csrf
                                    <button type="submit" class="btn btn-sm btn-danger">إلغاء</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">لا توجد طلبات إسعاف</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $requests->links() }}
    </div>
</div>
@endsection

@extends('Dashboard.layouts.master')
@section('title') طلبات الإسعاف @endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-table-card mb-3">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <strong>سيارات متاحة:</strong>
                <span class="badge badge-success">{{ $availableAmbulances->count() }}</span>
            </div>
            <small class="text-muted">الطلبات مرتبة حسب الأولوية: حرج → عاجل → عادي</small>
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
                    <th>الفرز</th>
                    <th>الحالة</th>
                    <th>السيارة</th>
                    <th>تحويل عيادة</th>
                    <th>العمليات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td>
                            {{ $req->patient_name }}<br>
                            <small>{{ $req->phone }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ \App\Models\AmbulanceRequest::$triageColors[$req->triage_level] ?? 'secondary' }}">
                                {{ \App\Models\AmbulanceRequest::$triageLabels[$req->triage_level] ?? $req->triage_level }}
                            </span>
                        </td>
                        <td>
                            <span class="badge hms-badge-status hms-badge-status--{{ $req->status }}">
                                {{ \App\Models\AmbulanceRequest::$statusLabels[$req->status] ?? $req->status }}
                            </span>
                            @if($req->timelines->count())
                                <ul class="small mb-0 mt-1 pl-3">
                                    @foreach($req->timelines as $tl)
                                        <li>{{ \App\Models\AmbulanceRequestTimeline::$statusLabels[$tl->status] ?? $tl->status }} — {{ $tl->recorded_at->format('H:i') }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td>{{ optional($req->ambulance)->car_number ?? '—' }}</td>
                        <td>
                            @if($req->transferred_to_clinic)
                                <span class="badge badge-info">نعم</span>
                                <small>{{ optional($req->section)->name }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-nowrap">
                            @if($req->status === 'pending' && $availableAmbulances->isNotEmpty())
                                <form action="{{ route('ambulance-requests.dispatch', $req) }}" method="POST" class="d-inline-flex gap-1 mb-1">
                                    @csrf
                                    <select name="ambulance_id" class="form-control form-control-sm" required>
                                        @foreach($availableAmbulances as $a)
                                            <option value="{{ $a->id }}">{{ $a->car_number }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-success">إرسال</button>
                                </form>
                            @endif
                            @if(in_array($req->status, ['dispatched','en_route','arrived','transported']))
                                <form action="{{ route('ambulance-requests.advance', $req) }}" method="POST" class="d-inline mb-1">
                                    @csrf
                                    @if($req->status === 'dispatched')
                                        <input type="hidden" name="status" value="en_route">
                                        <button class="btn btn-sm btn-warning">في الطريق</button>
                                    @elseif($req->status === 'en_route')
                                        <input type="hidden" name="status" value="arrived">
                                        <button class="btn btn-sm btn-info">وصلت</button>
                                    @elseif($req->status === 'arrived')
                                        <input type="hidden" name="status" value="transported">
                                        <button class="btn btn-sm btn-primary">نُقل المريض</button>
                                    @elseif($req->status === 'transported')
                                        <input type="hidden" name="status" value="completed">
                                        <button class="btn btn-sm btn-success">إكمال</button>
                                    @endif
                                </form>
                            @endif
                            @if(!$req->transferred_to_clinic && in_array($req->status, ['dispatched','en_route','arrived','transported']))
                                <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#transfer{{ $req->id }}">تحويل لعيادة</button>
                                @include('Dashboard.AmbulanceRequests.transfer_modal', ['req' => $req, 'sections' => $sections, 'doctors' => $doctors])
                            @endif
                            @if(in_array($req->status, ['pending','dispatched','en_route','arrived','transported']))
                                <form action="{{ route('ambulance-requests.cancel', $req) }}" method="POST" class="d-inline">@csrf
                                    <button class="btn btn-sm btn-danger">إلغاء</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">لا توجد طلبات</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $requests->links() }}
    </div>
</div>
@endsection

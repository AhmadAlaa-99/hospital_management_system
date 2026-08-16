<!-- Sidebar-right-->
@php
    $isAdminSide = auth('admin')->check();
    $pendingAppointmentsSide = $isAdminSide
        ? \App\Models\Appointment::with(['doctor', 'section'])->where('type', 'غير مؤكد')->latest()->take(8)->get()
        : collect();
    $sideNotifications = \App\Services\NotificationService::unreadForAuth()->take(10)->get();
    if ($sideNotifications->isEmpty()) {
        $sideNotifications = \App\Models\Notification::forAuthUser()->latest()->take(10)->get();
    }
    $recentDoctorsSide = \App\Models\Doctor::with(['section', 'image'])->latest()->take(8)->get();
    $sideUnreadCount = \App\Services\NotificationService::unreadForAuth()->count();
@endphp
<div class="sidebar sidebar-left sidebar-animate hms-alerts-panel">
    <div class="panel panel-primary card mb-0 box-shadow">
        <div class="tab-menu-heading border-0 p-3 hms-alerts-header">
            <div class="card-title mb-0">{{ app()->getLocale() === 'ar' ? 'لوحة التنبيهات' : 'Alerts Panel' }}</div>
            @if($sideUnreadCount > 0)
                <span class="badge badge-danger">{{ $sideUnreadCount }}</span>
            @endif
            <div class="card-options mr-auto">
                <a href="#" class="sidebar-remove"><i class="fe fe-x"></i></a>
            </div>
        </div>
        <div class="panel-body tabs-menu-body latest-tasks p-0 border-0">
            <div class="tabs-menu">
                <ul class="nav panel-tabs">
                    @if($isAdminSide)
                    <li>
                        <a href="#side1" class="active" data-toggle="tab">
                            <i class="ion ion-md-calendar tx-18 ml-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'المواعيد' : 'Appointments' }}
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="#side2" class="{{ $isAdminSide ? '' : 'active' }}" data-toggle="tab">
                            <i class="ion ion-md-notifications tx-18 ml-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications' }}
                        </a>
                    </li>
                    <li>
                        <a href="#side3" data-toggle="tab">
                            <i class="ion ion-md-medkit tx-18 ml-2"></i>
                            {{ app()->getLocale() === 'ar' ? 'الأطباء' : 'Doctors' }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content hms-alerts-content">
                @if($isAdminSide)
                <div class="tab-pane active" id="side1">
                    @forelse($pendingAppointmentsSide as $item)
                        <div class="list d-flex align-items-center border-bottom p-3 hms-alert-item">
                            <span class="avatar bg-primary brround avatar-md">{{ mb_substr($item->name, 0, 1) }}</span>
                            <a class="wrapper w-100 mr-3" href="{{ route('appointments.index') }}">
                                <p class="mb-0"><b>{{ $item->name }}</b></p>
                                <small class="text-muted">{{ optional($item->section)->name }} — {{ $item->created_at->diffForHumans() }}</small>
                            </a>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted">لا توجد مواعيد جديدة</div>
                    @endforelse
                    @if($pendingAppointmentsSide->isNotEmpty())
                        <div class="p-2 text-center border-top">
                            <a href="{{ route('appointments.index') }}" class="small">عرض كل المواعيد</a>
                        </div>
                    @endif
                </div>
                @endif

                <div class="tab-pane {{ $isAdminSide ? '' : 'active' }}" id="side2">
                    <div class="list-group list-group-flush">
                        @forelse($sideNotifications as $notification)
                            <a href="{{ route('notifications.read', $notification->id) }}" class="list-group-item hms-alert-item {{ $notification->reader_status ? '' : 'hms-alert-item--unread' }}">
                                <div class="d-flex align-items-start">
                                    <span class="avatar avatar-sm brround ml-2 bg-{{ $notification->reader_status ? 'secondary' : 'teal' }}">
                                        <i class="la la-bell text-white"></i>
                                    </span>
                                    <div>
                                        <strong class="d-block">{{ Str::limit($notification->message, 80) }}</strong>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">لا توجد إشعارات</div>
                        @endforelse
                    </div>
                    <div class="p-2 text-center border-top">
                        <a href="{{ route('notifications.index') }}" class="small font-weight-bold">عرض كل الإشعارات</a>
                    </div>
                </div>

                <div class="tab-pane" id="side3">
                    <div class="list-group list-group-flush">
                        @forelse($recentDoctorsSide as $doctor)
                            <div class="list-group-item hms-alert-item">
                                <div class="d-flex align-items-center">
                                    @if($doctor->image)
                                        <img class="avatar avatar-md brround ml-2" src="{{ URL::asset('Dashboard/img/doctors/'.$doctor->image->filename) }}" alt="">
                                    @else
                                        <span class="avatar avatar-md brround ml-2 bg-teal text-white">{{ mb_substr($doctor->name, 0, 1) }}</span>
                                    @endif
                                    <div>
                                        <div class="font-weight-semibold">{{ $doctor->name }}</div>
                                        <small class="text-muted">{{ optional($doctor->section)->name }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">لا يوجد أطباء</div>
                        @endforelse
                    </div>
                    @if($isAdminSide)
                        <div class="p-2 text-center border-top">
                            <a href="{{ route('Doctors.index') }}" class="small">عرض كل الأطباء</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--/Sidebar-right-->

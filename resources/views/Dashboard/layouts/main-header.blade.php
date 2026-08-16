<!-- main-header opened -->
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left ">
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>
            <div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block">
                <input class="form-control" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث...' : 'Search...' }}" type="search">
                <button class="btn"><i class="fas fa-search d-none d-md-block"></i></button>
            </div>
        </div>
        <div class="main-header-right">
            <div class="nav nav-item navbar-nav-right">
                <div class="dropdown nav-item d-none d-md-flex hms-lang-switch">
                    <a href="#" class="d-flex nav-item nav-link pl-0 country-flag1" data-toggle="dropdown"
                       aria-expanded="false">
                        @if (App::getLocale() == 'ar')
                            <span class="avatar country-Flag mr-0 align-self-center bg-transparent">
                                <img src="{{ URL::asset('Dashboard/img/flags/syria_flag.jpg') }}?v=3" alt="سوريا">
                            </span>
                        @else
                            <span class="avatar country-Flag mr-0 align-self-center bg-transparent">
                                <img src="{{URL::asset('Dashboard/img/flags/us_flag.jpg')}}" alt="USA">
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow" x-placement="bottom-end">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a class="dropdown-item" rel="alternate" hreflang="{{ $localeCode }}"
                               href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                @if($properties['native'] == "English")
                                    <i class="flag-icon flag-icon-us"></i>
                                @elseif($properties['native'] == "العربية")
                                    <img src="{{ URL::asset('Dashboard/img/flags/syria_flag.jpg') }}" alt="سوريا" style="width:22px;height:16px;object-fit:cover;border-radius:2px;margin-inline-end:6px;vertical-align:middle">
                                @endif
                                {{ $properties['native'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            <div class="nav-link" id="bs-example-navbar-collapse-1">
                    <form class="navbar-form" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search">
                            <span class="input-group-btn">
											<button type="reset" class="btn btn-default">
												<i class="fas fa-times"></i>
											</button>
											<button type="submit" class="btn btn-default nav-link resp-btn">
												<svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs"
                                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                     class="feather feather-search"><circle cx="11" cy="11"
                                                                                            r="8"></circle><line x1="21"
                                                                                                                 y1="21"
                                                                                                                 x2="16.65"
                                                                                                                 y2="16.65"></line></svg>
											</button>
										</span>
                        </div>
                    </form>
                </div>
                @php
                    $isAdminHeader = auth('admin')->check();
                    $headerPendingAppointments = $isAdminHeader
                        ? \App\Models\Appointment::with(['doctor','section'])->where('type','غير مؤكد')->latest()->take(5)->get()
                        : collect();
                    $headerPendingCount = $isAdminHeader
                        ? \App\Models\Appointment::where('type','غير مؤكد')->count()
                        : 0;
                    $headerNotifications = \App\Services\NotificationService::unreadForAuth()->take(8)->get();
                    $headerNotifCount = $headerNotifications->count();
                @endphp
                <div class="dropdown nav-item main-header-message ">
                    <a class="new nav-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-mail">
                            <path
                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        @if(($isAdminHeader && $headerPendingCount > 0) || (!$isAdminHeader && $headerNotifCount > 0))
                            <span class="pulse-danger"></span>
                        @endif
                    </a>
                    <div class="dropdown-menu">
                        <div class="menu-header-content bg-primary text-right">
                            <div class="d-flex">
                                <h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">
                                    {{ $isAdminHeader
                                        ? (app()->getLocale() === 'ar' ? 'طلبات المواعيد' : 'Appointment Requests')
                                        : (app()->getLocale() === 'ar' ? 'الرسائل' : 'Messages') }}
                                </h6>
                            </div>
                            <p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12">
                                @if($isAdminHeader)
                                    {{ app()->getLocale() === 'ar' ? 'لديك' : 'You have' }}
                                    {{ $headerPendingCount }}
                                    {{ app()->getLocale() === 'ar' ? 'موعد بانتظار التأكيد' : 'pending appointments' }}
                                @else
                                    {{ app()->getLocale() === 'ar' ? 'لديك' : 'You have' }}
                                    {{ $headerNotifCount }}
                                    {{ app()->getLocale() === 'ar' ? 'إشعار جديد' : 'new alerts' }}
                                @endif
                            </p>
                        </div>
                        <div class="main-message-list chat-scroll">
                            @if($isAdminHeader)
                                @forelse($headerPendingAppointments as $appointment)
                                    <a href="{{ route('appointments.index') }}" class="p-3 d-flex border-bottom">
                                        <div class="drop-img bg-teal text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;">
                                            {{ mb_substr($appointment->name, 0, 1) }}
                                        </div>
                                        <div class="wd-90p mr-2">
                                            <div class="d-flex">
                                                <h5 class="mb-1 name">{{ $appointment->name }}</h5>
                                            </div>
                                            <p class="mb-0 desc">
                                                {{ optional($appointment->section)->name }}
                                                -
                                                {{ optional($appointment->doctor)->name }}
                                            </p>
                                            <p class="time mb-0 text-left float-right mr-2 mt-2">{{ $appointment->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد طلبات حالياً' : 'No pending requests' }}
                                    </div>
                                @endforelse
                            @else
                                @forelse($headerNotifications as $notification)
                                    <a href="{{ route('notifications.read', $notification->id) }}" class="p-3 d-flex border-bottom">
                                        <div class="drop-img bg-teal text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;border-radius:50%;">
                                            ن
                                        </div>
                                        <div class="wd-90p mr-2">
                                            <div class="d-flex">
                                                <h5 class="mb-1 name">{{ $notification->message }}</h5>
                                            </div>
                                            <p class="time mb-0 text-left float-right mr-2 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <div class="p-4 text-center text-muted">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد رسائل حالياً' : 'No messages' }}
                                    </div>
                                @endforelse
                            @endif
                        </div>
                        <div class="text-center dropdown-footer">
                            @if($isAdminHeader)
                                <a href="{{ route('appointments.index') }}">{{ app()->getLocale() === 'ar' ? 'عرض كل المواعيد' : 'VIEW ALL APPOINTMENTS' }}</a>
                            @else
                                <a href="{{ route('notifications.index') }}">{{ app()->getLocale() === 'ar' ? 'عرض كل الإشعارات' : 'VIEW ALL NOTIFICATIONS' }}</a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="dropdown nav-item main-header-notification">
                    <a class="new nav-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-bell">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        @if($headerNotifCount > 0)
                            <span class="pulse"></span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-notifications">
                        <div class="menu-header-content bg-primary text-right">
                            <div class="d-flex">
                                <h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">
                                    {{ app()->getLocale() === 'ar' ? 'الإشعارات' : 'Notifications' }}
                                </h6>
                                @if($headerNotifCount > 0)
                                    <a href="{{ route('notifications.readAll') }}"
                                       class="badge badge-pill badge-warning mr-auto my-auto float-left text-dark">
                                        {{ app()->getLocale() === 'ar' ? 'تعليم الكل كمقروء' : 'Mark All Read' }}
                                    </a>
                                @endif
                            </div>
                            <p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12 notif-count">
                                {{ $headerNotifCount }}
                                {{ app()->getLocale() === 'ar' ? 'إشعار غير مقروء' : 'unread notifications' }}
                            </p>
                        </div>
                        <div class="main-notification-list Notification-scroll">
                            @forelse($headerNotifications as $notification)
                                <a class="d-flex p-3 border-bottom" href="{{ route('notifications.read', $notification->id) }}">
                                    <div class="notifyimg bg-pink">
                                        <i class="la la-file-alt text-white"></i>
                                    </div>
                                    <div class="mr-3">
                                        <h5 class="notification-label mb-1">{{ $notification->message }}</h5>
                                        <div class="notification-subtext">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="mr-auto">
                                        <i class="las la-angle-left text-left text-muted"></i>
                                    </div>
                                </a>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد إشعارات جديدة' : 'No new notifications' }}
                                </div>
                            @endforelse
                        </div>
                        <div class="dropdown-footer">
                            <a href="{{ route('notifications.index') }}">{{ app()->getLocale() === 'ar' ? 'عرض كل الإشعارات' : 'VIEW ALL NOTIFICATIONS' }}</a>
                        </div>
                    </div>
                </div>
                <div class="nav-item full-screen fullscreen-button">
                    <a class="new nav-link full-screen-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-maximize">
                            <path
                                d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                        </svg>
                    </a>
                </div>
                <div class="dropdown main-profile-menu nav nav-item nav-link">
                    <a class="profile-user d-flex" href="">
                        <img alt="user" src="{{ URL::asset('Dashboard/img/faces/default-avatar.png') }}" class="hms-user-avatar" onerror="this.src='{{ URL::asset('Dashboard/img/faces/default-avatar.png') }}'">
                    </a>
                    <div class="dropdown-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user">
                                    <img alt="user" src="{{ URL::asset('Dashboard/img/faces/default-avatar.png') }}" class="hms-user-avatar">
                                </div>
                                <div class="mr-3 my-auto">
                                    <h6>{{auth()->user()->name}}</h6><span>{{auth()->user()->email}}</span>
                                </div>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bx bx-user-circle"></i>الملف الشخصي</a>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bx bx-cog"></i>تعديل الملف الشخصي</a>
                        @if(auth('web')->check())
                            <form method="POST" action="{{ route('logout.user') }}">
                                @elseif(auth('admin')->check())
                                    <form method="POST" action="{{ route('logout.admin') }}">
                                        @elseif(auth('doctor')->check())
                                            <form method="POST" action="{{ route('logout.doctor') }}">
                                                @elseif(auth('ray_employee')->check())
                                                    <form method="POST" action="{{ route('logout.ray_employee') }}">
                                                        @elseif(auth('laboratorie_employee')->check())
                                                            <form method="POST"
                                                                  action="{{ route('logout.laboratorie_employee') }}">
                                                                @else
                                                                    <form method="POST"
                                                                          action="{{ route('logout.patient') }}">
                                                                        @endif
                                                                        @csrf
                                                                        <a class="dropdown-item" href="#"
                                                                           onclick="event.preventDefault();
                                        this.closest('form').submit();"><i class="bx bx-log-out"></i>تسجيل الخروج</a>
                                                                    </form>

                    </div>
                </div>
                <div class="dropdown main-header-message right-toggle">
                    <a class="nav-link pr-0" data-toggle="sidebar-left" data-target=".sidebar-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-menu">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(config('broadcasting.default') && config('broadcasting.default') !== 'null')
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script>
    if (typeof Echo !== 'undefined') {
        Echo.private('create-invoice.{{ auth()->user()->id }}').listen('.create-invoice', (data) => {
            var notificationsWrapper = $('.dropdown-notifications');
            var notifications = notificationsWrapper.find('h4.notification-label');
            var new_message = notificationsWrapper.find('.new_message');
            new_message.show();
            notifications.html('<h4 class="notification-label mb-1">' + data.message + data.patient + '</h4><div class="notification-subtext">' + data.created_at + '</div>');
            var countEl = notificationsWrapper.find('.notif-count');
            var current = parseInt(countEl.text()) || 0;
            countEl.text(current + 1);
        });
    }
</script>
@endif

<!-- /main-header -->

<div class="main-sidemenu">
    <div class="app-sidebar__user clearfix">
        <div class="dropdown user-pro-body">
            <div class="">
                <img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('Dashboard/img/faces/default-avatar.png')}}"><span class="avatar-status profile-status bg-green"></span>
            </div>
            <div class="user-info">
                <h4 class="font-weight-semibold mt-3 mb-0">{{ optional(\App\Helpers\DashboardAuth::user())->name }}</h4>
                <span class="mb-0 text-muted">{{ optional(\App\Helpers\DashboardAuth::user())->email }}</span>
            </div>
        </div>
    </div>
    <ul class="side-menu">
        <li class="side-item side-item-category">{{trans('main-sidebar_trans.Main')}}</li>
        <li class="slide">
            <a class="side-menu__item" href="{{ route('dashboard.doctor') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>
                <span class="side-menu__label">{{trans('main-sidebar_trans.index')}}</span>
            </a>
        </li>

        <li class="side-item side-item-category">{{trans('main-sidebar_trans.management')}}</li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3"/><path d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
                <span class="side-menu__label">كشوفات الدكتور</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('invoices.index') }}">قائمة الكشوفات</a></li>
                <li><a class="slide-item" href="{{route('completedInvoices')}}">قائمة الكشوفات المكتملة</a></li>
                <li><a class="slide-item" href="{{route('reviewInvoices')}}">قائمة المراجعات</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4C9.24 4 7 6.24 7 9c0 2.85 2.92 7.21 5 9.88 2.11-2.69 5-7 5-9.88 0-2.76-2.24-5-5-5zm0 7.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" opacity=".3"/><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zM7 9c0-2.76 2.24-5 5-5s5 2.24 5 5c0 2.88-2.88 7.19-5 9.88C9.92 16.21 7 11.85 7 9z"/><circle cx="12" cy="9" r="2.5"/></svg>
                <span class="side-menu__label">المواعيد</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('doctor.appointments') }}">المواعيد النشطة</a></li>
                <li><a class="slide-item" href="{{ route('doctor.appointments.finished') }}">المنتهية / المرفوضة</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" href="{{ route('doctor.queue.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M4 10h3v7H4zm6 1h3v6h-3zm6-2h3v8h-3z" opacity=".3"/><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zM7 10h3v7H7zm6-1h3v8h-3zm6-2h3v8h-3z"/></svg>
                <span class="side-menu__label">قائمة انتظار العيادة</span>
            </a>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3"/><path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z"/></svg>
                <span class="side-menu__label">المحادثات</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{route('list.patients')}}">قائمة المرضي</a></li>
                <li><a class="slide-item" href="{{route('chat.patients')}}">المحادثات الاخيرة</a></li>
            </ul>
        </li>
    </ul>
</div>

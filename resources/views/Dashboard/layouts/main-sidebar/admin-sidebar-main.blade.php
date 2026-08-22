<div class="main-sidemenu">
    <div class="app-sidebar__user clearfix">
        <div class="dropdown user-pro-body">
            <div class="">
                <img alt="user-img" class="avatar avatar-xl brround hms-user-avatar" src="{{ URL::asset('Dashboard/img/faces/default-avatar.png') }}"><span class="avatar-status profile-status bg-green"></span>
            </div>
            <div class="user-info">
                <h4 class="font-weight-semibold mt-3 mb-0">{{ optional(\App\Helpers\DashboardAuth::user())->name }}</h4>
                <span class="mb-0 text-muted">{{ optional(\App\Helpers\DashboardAuth::user())->email }}</span>
            </div>
        </div>
    </div>
    <ul class="side-menu">
        <li class="side-item side-item-category">{{ trans('main-sidebar_trans.Main') }}</li>
        <li class="slide">
            <a class="side-menu__item" href="{{ route('dashboard.admin') }}">
                <i class="fas fa-th-large side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.index') }}</span>
            </a>
        </li>

        <li class="side-item side-item-category">{{ trans('main-sidebar_trans.management') }}</li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-hospital side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.sections') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('Sections.index') }}">{{ trans('main-sidebar_trans.view_all') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-user-md side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.doctors') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('Doctors.index') }}">{{ trans('main-sidebar_trans.view_all') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-stethoscope side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.Services') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('Service.index') }}">{{ trans('main-sidebar_trans.Single_service') }}</a></li>
                <li><a class="slide-item" href="{{ route('Add_GroupServices') }}">{{ trans('main-sidebar_trans.group_services') }}</a></li>
                <li><a class="slide-item" href="{{ route('insurance.index') }}">{{ trans('main-sidebar_trans.Insurance') }}</a></li>
                <li><a class="slide-item" href="{{ route('Ambulance.index') }}">{{ trans('main-sidebar_trans.ambulance') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-users side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.patients') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('Patients.create') }}">{{ trans('main-sidebar_trans.add_patient') }}</a></li>
                <li><a class="slide-item" href="{{ route('Patients.index') }}">{{ trans('main-sidebar_trans.patients_list') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-file-invoice-dollar side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.invoices') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('single_invoices') }}">{{ trans('main-sidebar_trans.single_invoice') }}</a></li>
                <li><a class="slide-item" href="{{ route('group_invoices') }}">{{ trans('main-sidebar_trans.group_invoice') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-wallet side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.accounts') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('Receipt.index') }}">{{ trans('main-sidebar_trans.receipt') }}</a></li>
                <li><a class="slide-item" href="{{ route('Payment.index') }}">{{ trans('main-sidebar_trans.payment') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-x-ray side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.rays') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('ray_employee.index') }}">{{ trans('main-sidebar_trans.employees_list') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-flask side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.lab') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('laboratorie_employee.index') }}">{{ trans('main-sidebar_trans.employees_list') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-calendar-check side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">{{ trans('main-sidebar_trans.appointments') }}</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('appointments.index') }}">{{ trans('main-sidebar_trans.appointments_pending') }}</a></li>
                <li><a class="slide-item" href="{{ route('appointments.index2') }}">{{ trans('main-sidebar_trans.appointments_confirmed') }}</a></li>
                <li><a class="slide-item" href="{{ route('appointments.index3') }}">{{ trans('main-sidebar_trans.appointments_finished') }}</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" href="{{ route('admin.reports') }}">
                <i class="fas fa-chart-bar side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">التقارير والإحصائيات</span>
            </a>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-blog side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">المدونة</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('admin.blogs.index') }}">المقالات</a></li>
                <li><a class="slide-item" href="{{ route('admin.blogs.create') }}">مقال جديد</a></li>
                <li><a class="slide-item" href="{{ route('patient-testimonials.index') }}">مراجعات المرضى</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-file-medical side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">التأمين والإسعاف</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('insurance-claims.index') }}">مطالبات التأمين</a></li>
                <li><a class="slide-item" href="{{ route('insurance-claims.report') }}">تقرير التأمين</a></li>
                <li><a class="slide-item" href="{{ route('ambulance-requests.index') }}">طلبات الإسعاف</a></li>
                <li><a class="slide-item" href="{{ route('admin.referrals.index') }}">التحويلات بين التخصصات</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" data-toggle="slide" href="#">
                <i class="fas fa-pills side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">صيدلية وباقات</span><i class="angle fe fe-chevron-down"></i>
            </a>
            <ul class="slide-menu">
                <li><a class="slide-item" href="{{ route('pharmacy.index') }}">صيدلية العيادة</a></li>
                <li><a class="slide-item" href="{{ route('health-packages.index') }}">باقات الفحص</a></li>
            </ul>
        </li>

        <li class="slide">
            <a class="side-menu__item" href="{{ route('activity-logs.index') }}">
                <i class="fas fa-history side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">سجل النشاط</span>
            </a>
        </li>

        <li class="slide">
            <a class="side-menu__item" href="{{ route('admin.queue.index') }}">
                <i class="fas fa-list-ol side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">إدارة الانتظار</span>
            </a>
        </li>

        <li class="slide">
            <a class="side-menu__item" href="{{ route('doctor-schedules.index') }}">
                <i class="fas fa-clock side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">جدول الأطباء</span>
            </a>
        </li>

        <li class="slide">
            <a class="side-menu__item" href="{{ route('sham-cash-payments.index') }}">
                <i class="fas fa-mobile-alt side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">مدفوعات شام كاش</span>
                @php $pendingSham = \App\Models\ShamCashPayment::where('status', 'pending_review')->count(); @endphp
                @if($pendingSham > 0)
                    <span class="badge badge-warning mr-auto">{{ $pendingSham }}</span>
                @endif
            </a>
        </li>

        <li class="slide">
            <a class="side-menu__item" href="{{ route('site-settings.edit') }}">
                <i class="fas fa-cog side-menu__icon hms-side-fa"></i>
                <span class="side-menu__label">إعدادات الموقع</span>
            </a>
        </li>
    </ul>
</div>

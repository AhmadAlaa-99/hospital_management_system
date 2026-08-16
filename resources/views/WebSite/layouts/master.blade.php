<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <title>{{ optional($siteSetting)->hospital_name ?? 'Hospital Management System' }}</title>
    @include('WebSite.layouts.style')
    <link rel="stylesheet" href="{{ asset('WebSite/css/hms-website.css') }}">
    @livewireStyles
</head>

<body class="hms-body {{ app()->getLocale() === 'ar' ? 'hms-rtl' : 'hms-ltr' }}"
      data-patient-auth="{{ auth('patient')->check() ? '1' : '0' }}"
      data-patient-login-url="{{ route('website.patient.login') }}"
      data-patient-register-url="{{ route('website.patient.register') }}">

<div class="page-wrapper {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <div class="preloader"></div>

    <header class="main-header header-style-three hms-site-header">
        <div class="header-upper">
            <div class="inner-container clearfix">
                <div class="logo-outer">
                    <div class="logo">
                        <a href="{{ route('home') }}" class="hms-brand-link">
                            <img src="{{ asset('Dashboard/img/brand/hospital-logo.png') }}" alt="{{ optional($siteSetting)->hospital_name }}" class="hms-logo-img">
                            <span class="hms-brand-text">
                                @if(app()->getLocale() === 'ar')
                                    نظام <span>إدارة</span> المستشفى
                                @else
                                    Hospital <span>Management</span>
                                @endif
                            </span>
                        </a>
                    </div>
                </div>
                @include('WebSite.layouts.header')
            </div>
        </div>

        <div class="sticky-header">
            <div class="auto-container clearfix">
                <div class="logo pull-left">
                    <a href="{{ route('home') }}" class="img-responsive hms-brand-link">
                        <img src="{{ asset('Dashboard/img/brand/hospital-logo.png') }}" alt="{{ optional($siteSetting)->hospital_name }}" class="hms-logo-img">
                        <span class="hms-brand-text sticky">
                            @if(app()->getLocale() === 'ar')
                                نظام <span>إدارة</span> المستشفى
                            @else
                                Hospital <span>Management</span>
                            @endif
                        </span>
                    </a>
                </div>
                <div class="right-col pull-right">
                    <nav class="main-menu navbar-expand-md">
                        <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent1">
                            <ul class="navigation clearfix"></ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>

        <div class="mobile-menu">
            <div class="menu-backdrop"></div>
            <div class="close-btn"><span class="icon far fa-window-close"></span></div>
            <nav class="menu-box">
                <div class="nav-logo">
                    <a href="{{ route('home') }}" class="hms-brand-link">
                        <img src="{{ asset('Dashboard/img/brand/hospital-logo.png') }}" alt="{{ optional($siteSetting)->hospital_name }}" class="hms-logo-img">
                        <span class="hms-brand-text sticky">
                            @if(app()->getLocale() === 'ar')
                                نظام <span>إدارة</span> المستشفى
                            @else
                                Hospital <span>Management</span>
                            @endif
                        </span>
                    </a>
                </div>
                <ul class="navigation clearfix"></ul>
            </nav>
        </div>
    </header>

    @yield('content')

    @include('WebSite.layouts.footer')
</div>

<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-angle-up"></span></div>

{{-- لوحة معلومات المستشفى (ديناميكية) --}}
<div class="xs-sidebar-group info-group">
    <div class="xs-overlay xs-bg-black"></div>
    <div class="xs-sidebar-widget">
        <div class="sidebar-widget-container">
            <div class="widget-heading">
                <a href="#" class="close-side-widget">X</a>
            </div>
            <div class="sidebar-textwidget">
                <div class="sidebar-info-contents">
                    <div class="content-inner">
                        <div class="logo">
                            <a href="{{ route('home') }}" class="hms-brand-link sticky">
                                <img src="{{ asset('Dashboard/img/brand/hospital-logo.png') }}" alt="{{ optional($siteSetting)->hospital_name }}" class="hms-logo-img"/>
                                <span class="hms-brand-text sticky">
                                    @if(app()->getLocale() === 'ar')
                                        نظام <span>إدارة</span> المستشفى
                                    @else
                                        Hospital <span>Management</span>
                                    @endif
                                </span>
                            </a>
                        </div>
                        <div class="content-box">
                            <h2>{{ optional($siteSetting)->hospital_name ?? 'عن المستشفى' }}</h2>
                            <p class="text">{{ optional($siteSetting)->about }}</p>
                            <a href="{{ route('home') }}#appointment" class="theme-btn btn-style-two">
                                <span class="txt">احجز موعد</span>
                            </a>
                        </div>
                        <div class="contact-info">
                            <h2>معلومات التواصل</h2>
                            <ul class="list-style-two">
                                <li>
                                    <span class="icon flaticon-map"></span>
                                    {{ optional($siteSetting)->address }}
                                    @if(optional($siteSetting)->city)
                                        <br>{{ $siteSetting->city }}
                                    @endif
                                </li>
                                <li>
                                    <span class="icon flaticon-telephone"></span>
                                    <a href="tel:{{ optional($siteSetting)->phone }}">{{ optional($siteSetting)->phone }}</a>
                                    @if(optional($siteSetting)->phone2)
                                        <br><a href="tel:{{ $siteSetting->phone2 }}">{{ $siteSetting->phone2 }}</a>
                                    @endif
                                </li>
                                <li>
                                    <span class="icon flaticon-message-1"></span>
                                    <a href="mailto:{{ optional($siteSetting)->email }}">{{ optional($siteSetting)->email }}</a>
                                </li>
                                <li>
                                    <span class="icon flaticon-timetable"></span>
                                    {{ optional($siteSetting)->working_hours }}
                                </li>
                            </ul>
                        </div>
                        <ul class="social-box">
                            @if(optional($siteSetting)->facebook)
                                <li class="facebook"><a href="{{ $siteSetting->facebook }}" target="_blank" class="fab fa-facebook-f"></a></li>
                            @endif
                            @if(optional($siteSetting)->twitter)
                                <li class="twitter"><a href="{{ $siteSetting->twitter }}" target="_blank" class="fab fa-twitter"></a></li>
                            @endif
                            @if(optional($siteSetting)->linkedin)
                                <li class="linkedin"><a href="{{ $siteSetting->linkedin }}" target="_blank" class="fab fa-linkedin-in"></a></li>
                            @endif
                            @if(optional($siteSetting)->instagram)
                                <li class="instagram"><a href="{{ $siteSetting->instagram }}" target="_blank" class="fab fa-instagram"></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('WebSite.partials.patient-auth-modal')
@include('WebSite.layouts.scripts')
@livewireScripts
@stack('scripts')
</body>
</html>

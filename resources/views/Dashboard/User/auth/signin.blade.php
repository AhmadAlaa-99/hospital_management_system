@extends('Dashboard.layouts.master2')
@section('title')
{{ trans('Dashboard/login_trans.Welcome_sub') }}
@endsection
@section('css')
<style>
    .panel { display: none; }
    .hms-login-page {
        min-height: 100vh;
    }
    .hms-login-hero {
        background:
            linear-gradient(135deg, rgba(13, 115, 119, 0.88), rgba(20, 163, 168, 0.75)),
            url("{{ URL::asset('Dashboard/img/media/hospital-login.jpg') }}") center/cover no-repeat;
        position: relative;
    }
    .hms-login-hero-content {
        color: #fff;
        max-width: 520px;
        margin: auto;
        padding: 2rem;
        text-align: center;
    }
    .hms-login-hero-content h2 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: .75rem;
    }
    .hms-login-hero-content p {
        opacity: .95;
        font-size: 1.05rem;
        line-height: 1.7;
    }
    .hms-brand {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.75rem;
        text-decoration: none;
    }
    .hms-brand img {
        width: 48px;
        height: 48px;
        object-fit: contain;
        border-radius: 12px;
        background: #f4fbfb;
        padding: 4px;
        border: 1px solid #d7ecec;
    }
    .hms-brand-title {
        margin: 0;
        color: #0d7377;
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.3;
    }
    .hms-brand-title span {
        color: #1499a0;
    }
    .hms-login-card h2.main-title {
        color: #163a3c;
        font-weight: 700;
        margin-bottom: .35rem;
    }
    .hms-login-card .sub-title {
        color: #6b7c7d;
        margin-bottom: 1.5rem;
    }
    .hms-login-card .form-control,
    .hms-login-card .btn-main-primary {
        border-radius: 10px;
        min-height: 46px;
    }
    .hms-login-card .btn-main-primary {
        background: linear-gradient(135deg, #0d7377, #14a3a8);
        border: 0;
        font-weight: 600;
    }
    .hms-login-card .btn-main-primary:hover {
        opacity: .95;
    }
    .hms-panel-title {
        font-size: 1.15rem;
        color: #0d7377;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    .hms-back-home {
        display: inline-block;
        margin-top: 1.5rem;
        color: #0d7377;
        font-weight: 600;
    }
    .hms-back-home:hover {
        color: #0a5c5f;
        text-decoration: none;
    }
</style>
<link href="{{URL::asset('Dashboard/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}" rel="stylesheet">
@endsection
@section('content')
<div class="container-fluid hms-login-page">
    <div class="row no-gutter">
        <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex hms-login-hero">
            <div class="hms-login-hero-content">
                <img src="{{ URL::asset('Dashboard/img/brand/hospital-logo.png') }}" alt="Hospital Logo" style="width:84px;height:84px;margin-bottom:1rem;border-radius:18px;background:#fff;padding:8px;">
                <h2>{{ trans('Dashboard/login_trans.Welcome_sub') }}</h2>
                <p>{{ trans('Dashboard/login_trans.secure_note') }}</p>
            </div>
        </div>

        <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
            <div class="login d-flex align-items-center py-2">
                <div class="container p-0">
                    <div class="row">
                        <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                            <div class="card-sigin hms-login-card">
                                <a href="{{ url('/') }}" class="hms-brand">
                                    <img src="{{ URL::asset('Dashboard/img/brand/hospital-logo.png') }}" alt="logo">
                                    <h1 class="hms-brand-title">
                                        @if(app()->getLocale() === 'ar')
                                            نظام <span>إدارة</span> المستشفى
                                        @else
                                            Hospital <span>Management</span>
                                        @endif
                                    </h1>
                                </a>

                                <div class="main-signup-header">
                                    <h2 class="main-title">{{ trans('Dashboard/login_trans.Welcome') }}</h2>
                                    <p class="sub-title">{{ trans('Dashboard/login_trans.secure_note') }}</p>

                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="sectionChooser">{{ trans('Dashboard/login_trans.Select_Enter') }}</label>
                                        <select class="form-control" id="sectionChooser">
                                            <option value="" selected disabled>{{ trans('Dashboard/login_trans.Choose_list') }}</option>
                                            <option value="user">{{ trans('Dashboard/login_trans.user') }}</option>
                                            <option value="admin">{{ trans('Dashboard/login_trans.admin') }}</option>
                                            <option value="doctor">{{ trans('Dashboard/login_trans.doctor') }}</option>
                                            <option value="ray_employee">{{ trans('Dashboard/login_trans.ray_employee') }}</option>
                                            <option value="laboratorie_employee">{{ trans('Dashboard/login_trans.laboratorie_employee') }}</option>
                                        </select>
                                    </div>

                                    <div class="panel" id="user">
                                        <h3 class="hms-panel-title">{{ trans('Dashboard/login_trans.login_as_patient') }}</h3>
                                        <form method="POST" action="{{ route('login.patient') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Email') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Email_placeholder') }}" type="email" name="email" value="{{ old('email') }}" required autofocus>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Password') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Password_placeholder') }}" type="password" name="password" required autocomplete="current-password">
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block">{{ trans('Dashboard/login_trans.Sign_In') }}</button>
                                        </form>
                                    </div>

                                    <div class="panel" id="admin">
                                        <h3 class="hms-panel-title">{{ trans('Dashboard/login_trans.login_as_admin') }}</h3>
                                        <form method="POST" action="{{ route('login.admin') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Email') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Email_placeholder') }}" type="email" name="email" value="{{ old('email') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Password') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Password_placeholder') }}" type="password" name="password" required autocomplete="current-password">
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block">{{ trans('Dashboard/login_trans.Sign_In') }}</button>
                                        </form>
                                    </div>

                                    <div class="panel" id="doctor">
                                        <h3 class="hms-panel-title">{{ trans('Dashboard/login_trans.login_as_doctor') }}</h3>
                                        <form method="POST" action="{{ route('login.doctor') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Email') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Email_placeholder') }}" type="email" name="email" value="{{ old('email') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Password') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Password_placeholder') }}" type="password" name="password" required autocomplete="current-password">
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block">{{ trans('Dashboard/login_trans.Sign_In') }}</button>
                                        </form>
                                    </div>

                                    <div class="panel" id="ray_employee">
                                        <h3 class="hms-panel-title">{{ trans('Dashboard/login_trans.login_as_ray') }}</h3>
                                        <form method="POST" action="{{ route('login.ray_employee') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Email') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Email_placeholder') }}" type="email" name="email" value="{{ old('email') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Password') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Password_placeholder') }}" type="password" name="password" required autocomplete="current-password">
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block">{{ trans('Dashboard/login_trans.Sign_In') }}</button>
                                        </form>
                                    </div>

                                    <div class="panel" id="laboratorie_employee">
                                        <h3 class="hms-panel-title">{{ trans('Dashboard/login_trans.login_as_lab') }}</h3>
                                        <form method="POST" action="{{ route('login.laboratorie_employee') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Email') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Email_placeholder') }}" type="email" name="email" value="{{ old('email') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ trans('Dashboard/login_trans.Password') }}</label>
                                                <input class="form-control" placeholder="{{ trans('Dashboard/login_trans.Password_placeholder') }}" type="password" name="password" required autocomplete="current-password">
                                            </div>
                                            <button type="submit" class="btn btn-main-primary btn-block">{{ trans('Dashboard/login_trans.Sign_In') }}</button>
                                        </form>
                                    </div>

                                    <a href="{{ url('/') }}" class="hms-back-home">{{ trans('Dashboard/login_trans.Back_Home') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $('#sectionChooser').change(function () {
        var myID = $(this).val();
        $('.panel').each(function () {
            myID === $(this).attr('id') ? $(this).show() : $(this).hide();
        });
    });
</script>
@endsection

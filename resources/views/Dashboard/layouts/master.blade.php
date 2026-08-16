<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Hospital Management System - Graduation Project">
		<meta name="Author" content="Hospital Management System">
		<meta name="Keywords" content="hospital, doctors, patients, appointments, management"/>
		<title>@yield('title', trans('main-sidebar_trans.Main'))</title>
		@include('Dashboard.layouts.head')
	</head>

	<body class="main-body app sidebar-mini">
		<div id="global-loader" class="hms-global-loader">
			<div class="hms-loader-box">
				<img src="{{ URL::asset('Dashboard/img/hms-loader.svg') }}?v=2" class="loader-img hms-loader-img" alt="Loading">
				<p class="hms-loader-text">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</p>
			</div>
		</div>
		@include('Dashboard.layouts.main-sidebar')
		<div class="main-content app-content">
			@include('Dashboard.layouts.main-header')
			<div class="container-fluid">
				@yield('page-header')
				@yield('content')
				@include('Dashboard.layouts.sidebar')
				@include('Dashboard.layouts.models')
            	@include('Dashboard.layouts.footer')
				@include('Dashboard.layouts.footer-scripts')
	</body>
</html>

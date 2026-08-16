<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Hospital Management System - Graduation Project">
		<meta name="Author" content="Hospital Management System">
		<meta name="Keywords" content="hospital, management, doctors, patients, appointments"/>
		@include('Dashboard.layouts.head')
	</head>

	<body class="main-body bg-primary-transparent">
		<div id="global-loader" class="hms-global-loader">
			<div class="hms-loader-box">
				<img src="{{ URL::asset('Dashboard/img/hms-loader.svg') }}?v=2" class="loader-img hms-loader-img" alt="Loading">
				<p class="hms-loader-text">{{ app()->getLocale() === 'ar' ? 'جاري التحميل...' : 'Loading...' }}</p>
			</div>
		</div>
		@yield('content')
		@include('Dashboard.layouts.footer-scripts')
	</body>
</html>

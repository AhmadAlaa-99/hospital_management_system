@extends('Dashboard.layouts.master')
@section('title')
    إعدادات الموقع
@endsection
@section('css')
    <link href="{{URL::asset('dashboard/plugins/notify/css/notifIt.css')}}" rel="stylesheet"/>
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">إعدادات الموقع</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الفوتر وبيانات التواصل</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('Dashboard.messages_alert')
    <div class="row">
        <div class="col-xl-10">
            <div class="card hms-form-card">
                <div class="card-header"><h5 class="mb-0">بيانات المستشفى (تظهر في الفوتر)</h5></div>
                <div class="card-body">
                    <form action="{{ route('site-settings.update') }}" method="POST" class="hms-form-box">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>اسم المستشفى</label>
                                <input type="text" name="hospital_name" class="form-control" value="{{ old('hospital_name', $setting->hospital_name) }}" required>
                                <small class="text-muted">مثال: مستشفى الشام التخصصي للعيادات الشاملة التخصصية</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>المدينة</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city', $setting->city) }}">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>العنوان</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $setting->address) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>الهاتف</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>هاتف إضافي / موبايل</label>
                                <input type="text" name="phone2" class="form-control" value="{{ old('phone2', $setting->phone2) }}">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>البريد</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>ساعات العمل</label>
                                <input type="text" name="working_hours" class="form-control" value="{{ old('working_hours', $setting->working_hours) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>واتساب</label>
                                <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $setting->whatsapp) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Facebook</label>
                                <input type="text" name="facebook" class="form-control" value="{{ old('facebook', $setting->facebook) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Twitter</label>
                                <input type="text" name="twitter" class="form-control" value="{{ old('twitter', $setting->twitter) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Instagram</label>
                                <input type="text" name="instagram" class="form-control" value="{{ old('instagram', $setting->instagram) }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>LinkedIn</label>
                                <input type="text" name="linkedin" class="form-control" value="{{ old('linkedin', $setting->linkedin) }}">
                            </div>
                            <div class="col-md-12 form-group">
                                <label>نبذة عن المستشفى</label>
                                <textarea name="about" class="form-control" rows="3">{{ old('about', $setting->about) }}</textarea>
                            </div>
                            <div class="col-md-12 form-group">
                                <label>نص الحقوق</label>
                                <input type="text" name="copyright" class="form-control" value="{{ old('copyright', $setting->copyright) }}">
                            </div>
                        </div>
                        <button class="btn btn-primary btn-hms-primary" type="submit">حفظ الإعدادات</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('dashboard/plugins/notify/js/notifIt.js')}}"></script>
@endsection

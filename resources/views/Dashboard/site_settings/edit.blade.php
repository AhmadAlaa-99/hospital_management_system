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
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ الفوتر والدفع</span>
            </div>
        </div>
    </div>
@endsection
@section('content')
    @include('Dashboard.messages_alert')
    <div class="row">
        <div class="col-xl-10">
            <div class="card hms-form-card">
                <div class="card-header pb-0 border-bottom-0">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-hospital" role="tab">
                                <i class="fas fa-hospital ml-1"></i> بيانات المستشفى
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-sham-cash" role="tab">
                                <i class="fas fa-wallet ml-1"></i> شام كاش
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab-hospital" role="tabpanel">
                            <form action="{{ route('site-settings.update') }}" method="POST" class="hms-form-box" enctype="multipart/form-data">
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
                        <div class="tab-pane fade" id="tab-sham-cash" role="tabpanel">
                            <form action="{{ route('site-settings.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="sham_cash_section" value="1">
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="sham_cash_enabled" value="1" class="form-check-input" id="shamCashEnabled" {{ $setting->sham_cash_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label" for="shamCashEnabled">تفعيل الدفع عبر شام كاش للمرضى</label>
                                </div>
                                <div class="form-group">
                                    <label>عنوان المحفظة (Sham Cash Wallet)</label>
                                    <input type="text" name="sham_cash_wallet" class="form-control" value="{{ old('sham_cash_wallet', $setting->sham_cash_wallet) }}" placeholder="مثال: 09xxxxxxxx">
                                </div>
                                <div class="form-group">
                                    <label>QR Code للدفع</label>
                                    @if($setting->sham_cash_qr_path)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $setting->sham_cash_qr_path) }}" alt="QR" style="max-width:150px;border:1px solid #ddd;padding:4px;">
                                        </div>
                                    @endif
                                    <input type="file" name="sham_cash_qr" class="form-control" accept="image/*">
                                    <small class="text-muted">ارفع صورة QR من تطبيق شام كاش</small>
                                </div>
                                <div class="form-group">
                                    <label>تعليمات الدفع للمريض</label>
                                    <textarea name="sham_cash_instructions" class="form-control" rows="3" placeholder="مثال: ادفع المبلغ ثم ارفع screenshot الإيصال">{{ old('sham_cash_instructions', $setting->sham_cash_instructions) }}</textarea>
                                </div>
                                <button class="btn btn-success" type="submit">حفظ إعدادات شام كاش</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('dashboard/plugins/notify/js/notifIt.js')}}"></script>
    @if(old('sham_cash_section') || request('tab') === 'sham-cash')
    <script>
        $(function () { $('a[href="#tab-sham-cash"]').tab('show'); });
    </script>
    @endif
@endsection

@extends('Dashboard.layouts.master')
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto"><h4 class="content-title mb-0">تقييم الطبيب</h4></div>
</div>
@endsection
@section('content')
@include('Dashboard.messages_alert')
<div class="card hms-form-card col-md-8">
    <div class="card-body">
        <p class="mb-3">موعد: <strong>{{ $appointment->appointment }}</strong> — د. {{ optional($appointment->doctor)->name }}</p>

        <form action="{{ route('patient.rate.store', $appointment) }}" method="POST">
            @csrf
            <div class="form-group">
                <label>التقييم (1-5)</label>
                <select name="rating" class="form-control @error('rating') is-invalid @enderror" required>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ (int) old('rating', 5) === $i ? 'selected' : '' }}>{{ $i }} نجوم</option>
                    @endfor
                </select>
                @error('rating')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label>مراجعتك (اختياري)</label>
                <textarea name="comment" rows="4" class="form-control @error('comment') is-invalid @enderror"
                          placeholder="اكتب تجربتك مع الطبيب والخدمة...">{{ old('comment') }}</textarea>
                @error('comment')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="share_on_homepage"
                           name="share_on_homepage" value="1" {{ old('share_on_homepage') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="share_on_homepage">
                        أرغب بنشر مراجعتي في قسم «ماذا يقول المرضى» بالصفحة الرئيسية
                    </label>
                </div>
                <small class="form-text text-muted">
                    تُراجع مراجعتك من الإدارة قبل النشر. يُفضّل كتابة 15 حرفاً على الأقل.
                </small>
            </div>

            <button type="submit" class="btn btn-success">إرسال التقييم</button>
            <a href="{{ route('patient.appointments') }}" class="btn btn-secondary">رجوع</a>
        </form>
    </div>
</div>
@endsection

@php
    $loggedInPatient = auth('patient')->user();
@endphp
<div class="hms-appointment-form {{ $loggedInPatient ? '' : 'hms-appointment-form--guest' }}">
    @unless($loggedInPatient)
        <div class="hms-appointment-auth-notice">
            <i class="fas fa-user-lock"></i>
            <div>
                <strong>حجز المواعيد للمرضى المسجّلين فقط</strong>
                <p>سجّل دخولك أو أنشئ حساباً مريضاً لإرسال طلب الموعد.</p>
            </div>
            <button type="button" class="theme-btn btn-style-one btn-sm" id="hmsAppointmentLoginBtn">
                <span class="txt">دخول / إنشاء حساب</span>
            </button>
        </div>
    @else
        <div class="hms-appointment-user-bar">
            <i class="fas fa-user-check"></i>
            <span>الحجز باسم: <strong>{{ $loggedInPatient->name }}</strong></span>
            <span class="hms-appointment-user-bar__meta">{{ $loggedInPatient->email }} · {{ $loggedInPatient->Phone }}</span>
        </div>
    @endunless

    <form id="hmsAppointmentForm" method="POST" action="{{ route('appointments.book') }}">
        @csrf
        <div class="row clearfix">
            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <label for="sectionSelect"><i class="fas fa-hospital-alt"></i> القسم</label>
                <select class="form-select" name="section_id" id="sectionSelect" required {{ $loggedInPatient ? '' : 'disabled' }}>
                    <option value="">-- اختار القسم --</option>
                    @foreach($sections as $sectionItem)
                        <option value="{{ $sectionItem->id }}">{{ $sectionItem->name }}</option>
                    @endforeach
                </select>
                <span class="text-danger d-block hms-field-error" data-error-for="section_id"></span>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <label for="doctorSelect"><i class="fas fa-user-md"></i> الدكتور</label>
                <select name="doctor_id" class="form-select" id="doctorSelect" required disabled>
                    <option value="">-- اختار الدكتور --</option>
                </select>
                <span class="text-danger d-block hms-field-error" data-error-for="doctor_id"></span>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <label for="preferredDate"><i class="fas fa-calendar"></i> التاريخ المفضل</label>
                <input type="date" name="preferred_date" id="preferredDate" class="form-control" min="{{ date('Y-m-d') }}" required {{ $loggedInPatient ? '' : 'disabled' }}>
                <span class="text-danger d-block hms-field-error" data-error-for="preferred_date"></span>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                <label for="preferredTime"><i class="fas fa-clock"></i> الوقت المفضل</label>
                <select name="preferred_time" id="preferredTime" class="form-select" required disabled>
                    <option value="">-- اختر الطبيب والتاريخ أولاً --</option>
                </select>
                <span class="text-danger d-block hms-field-error" data-error-for="preferred_time"></span>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                <label><i class="fas fa-video"></i> نوع الاستشارة</label>
                <select name="consultation_type" id="consultationType" class="form-select" {{ $loggedInPatient ? '' : 'disabled' }}>
                    <option value="in_person">حضوري — في العيادة</option>
                    <option value="telemedicine">عن بُعد — استشارة فيديو</option>
                </select>
                <small class="text-muted d-block mt-1">عند اختيار «عن بُعد» يُنشأ رابط الاجتماع تلقائياً.</small>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 form-group d-none" id="meetingUrlGroup">
                <label for="meetingUrl"><i class="fas fa-link"></i> رابط الاجتماع (اختياري)</label>
                <input type="url" name="meeting_url" id="meetingUrl" class="form-control" placeholder="يُترك فارغاً للإنشاء التلقائي" {{ $loggedInPatient ? '' : 'disabled' }}>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                <textarea name="notes" id="appointmentNotes" placeholder="ملاحظات" {{ $loggedInPatient ? '' : 'disabled' }}>{{ old('notes') }}</textarea>
                <span class="text-danger d-block hms-field-error" data-error-for="notes"></span>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                <button class="theme-btn btn-style-two" type="submit" id="appointmentSubmitBtn" {{ $loggedInPatient ? '' : 'disabled' }}>
                    <span class="txt" id="appointmentSubmitText"><i class="fas fa-calendar-check"></i> تاكيد</span>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="hms-modal" id="hmsAppointmentModal" aria-hidden="true">
    <div class="hms-modal__backdrop" data-hms-modal-close></div>
    <div class="hms-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="hmsAppointmentModalTitle">
        <button type="button" class="hms-modal__close" data-hms-modal-close aria-label="إغلاق">&times;</button>
        <div class="hms-modal__icon"><i class="fas fa-check-circle"></i></div>
        <h3 id="hmsAppointmentModalTitle">تم استلام طلب الموعد</h3>
        <p id="hmsAppointmentModalMessage">
            تم ارسال تفاصيل الحجز الى المستشفى وسيتم ارسال معلومات الموعد عبر الهاتف والبريد الالكتروني
        </p>
        <button type="button" class="theme-btn btn-style-two" data-hms-modal-close>
            <span class="txt">حسناً</span>
        </button>
    </div>
</div>

@push('scripts')
<script>
window.hmsAppointmentDoctors = @json($appointmentDoctors ?? []);
window.hmsPatientLoggedIn = @json((bool) $loggedInPatient);
(function ($) {
    var doctorsBySection = window.hmsAppointmentDoctors || {};
    var $section = $('#sectionSelect');
    var $doctor = $('#doctorSelect');
    var $form = $('#hmsAppointmentForm');
    var $modal = $('#hmsAppointmentModal');

    if (!$form.length) {
        return;
    }

    function clearErrors() {
        $('.hms-field-error').text('');
    }

    function resetDoctors(placeholder) {
        $doctor.prop('disabled', true).html('<option value="">' + (placeholder || '-- اختار الدكتور --') + '</option>');
    }

    function loadDoctors(sectionId) {
        if (!sectionId) {
            resetDoctors();
            return;
        }

        var doctors = doctorsBySection[sectionId] || doctorsBySection[String(sectionId)] || [];
        if (!doctors.length) {
            resetDoctors('لا يوجد أطباء في هذا القسم');
            return;
        }

        var options = '<option value="">-- اختار الدكتور --</option>';
        doctors.forEach(function (doctor) {
            options += '<option value="' + doctor.id + '">' + $('<div>').text(doctor.name).html() + '</option>';
        });
        $doctor.html(options).prop('disabled', false);
    }

    function parsePossiblyCorruptJson(text) {
        if (!text) {
            return null;
        }
        var cleaned = String(text).replace(/^\u0000+/, '').trim();
        return cleaned ? JSON.parse(cleaned) : null;
    }

    function openModal(message) {
        if (message) {
            $('#hmsAppointmentModalMessage').text(message);
        }
        $modal.addClass('is-open').attr('aria-hidden', 'false');
        $('body').addClass('hms-modal-open');
    }

    function closeModal() {
        $modal.removeClass('is-open').attr('aria-hidden', 'true');
        $('body').removeClass('hms-modal-open');
    }

    function submitAppointment() {
        clearErrors();

        var $btn = $('#appointmentSubmitBtn');
        var $txt = $('#appointmentSubmitText');
        $btn.prop('disabled', true);
        $txt.html('<i class="fas fa-spinner fa-spin"></i> جاري الارسال...');

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'text',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).done(function (raw) {
            var res = {};
            try {
                res = parsePossiblyCorruptJson(raw) || {};
            } catch (err) {
                res = {};
            }
            openModal(res.message || 'تم ارسال تفاصيل الحجز الى المستشفى وسيتم ارسال معلومات الموعد عبر الهاتف والبريد الالكتروني');
            $form[0].reset();
            resetDoctors();
            $('#preferredTime').prop('disabled', true).html('<option value="">-- اختر الطبيب والتاريخ أولاً --</option>');
        }).fail(function (xhr) {
            var payload = null;
            try {
                payload = parsePossiblyCorruptJson(xhr.responseText);
            } catch (err) {
                payload = null;
            }

            if (xhr.status === 401 || (payload && payload.auth_required)) {
                if (window.hmsRequirePatientAuth) {
                    window.hmsRequirePatientAuth(submitAppointment);
                } else if (window.hmsOpenAuthModal) {
                    window.hmsOpenAuthModal(submitAppointment);
                }
                return;
            }

            if (xhr.status === 422 && payload && payload.errors) {
                Object.keys(payload.errors).forEach(function (field) {
                    $('[data-error-for="' + field + '"]').text(payload.errors[field][0]);
                });
            } else {
                openModal((payload && payload.message) || 'حدث خطأ أثناء ارسال الطلب، حاول مرة أخرى');
            }
        }).always(function () {
            $btn.prop('disabled', false);
            $txt.html('<i class="fas fa-calendar-check"></i> تاكيد');
        });
    }

    $section.on('change', function () {
        clearErrors();
        loadDoctors($(this).val());
        $('#preferredTime').prop('disabled', true).html('<option value="">-- اختر الطبيب والتاريخ --</option>');
    });

    $('#doctorSelect, #preferredDate').on('change', function () {
        var doctorId = $doctor.val();
        var date = $('#preferredDate').val();
        var $time = $('#preferredTime');
        if (!doctorId || !date) {
            $time.prop('disabled', true).html('<option value="">-- اختر الطبيب والتاريخ أولاً --</option>');
            return;
        }
        $time.prop('disabled', true).html('<option value="">جاري تحميل الأوقات...</option>');
        var slotsRequest = (window.hmsAjaxJson || function (opts) {
            return $.ajax($.extend({}, opts, { dataType: 'text' })).then(function (raw) {
                return (window.hmsParseJson || parsePossiblyCorruptJson)(raw) || {};
            });
        });
        slotsRequest({
            url: '{{ route("appointments.slots") }}',
            method: 'GET',
            data: { doctor_id: doctorId, date: date }
        }).done(function (res) {
            var slots = (res && res.slots) ? res.slots : [];
            if (!slots.length) {
                var hint = (res && res.message) ? res.message : 'لا توجد أوقات متاحة لهذا اليوم';
                $time.html('<option value="">' + $('<div>').text(hint).html() + '</option>').prop('disabled', true);
                return;
            }
            var opts = '<option value="">-- اختر الوقت --</option>';
            slots.forEach(function (s) {
                opts += '<option value="' + s + '">' + s + '</option>';
            });
            $time.html(opts).prop('disabled', false);
        }).fail(function () {
            $time.html('<option value="">تعذر تحميل الأوقات، حاول مرة أخرى</option>').prop('disabled', true);
        });
    });

    $(document).on('click', '[data-hms-modal-close]', closeModal);
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    $('#hmsAppointmentLoginBtn').on('click', function () {
        if (window.hmsOpenAuthModal) {
            window.hmsOpenAuthModal(function () {
                window.location.reload();
            });
        }
    });

    function toggleMeetingUrlField() {
        var isRemote = $('#consultationType').val() === 'telemedicine';
        $('#meetingUrlGroup').toggleClass('d-none', !isRemote);
    }

    $('#consultationType').on('change', toggleMeetingUrlField);
    toggleMeetingUrlField();

    @if(session('appointment_success'))
        openModal();
    @endif

    $form.on('submit', function (e) {
        e.preventDefault();

        var attemptSubmit = function () {
            submitAppointment();
        };

        if (window.hmsRequirePatientAuth && !window.hmsPatientLoggedIn) {
            window.hmsRequirePatientAuth(function () {
                window.location.reload();
            });
            return;
        }

        if (window.hmsRequirePatientAuth) {
            window.hmsRequirePatientAuth(attemptSubmit);
        } else {
            attemptSubmit();
        }
    });
})(jQuery);
</script>
@endpush

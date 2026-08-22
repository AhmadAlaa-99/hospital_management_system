<section class="hms-ambulance-section" id="ambulance">
    <div class="auto-container">
        <div class="sec-title centered">
            <h2>طلب إسعاف</h2>
            <div class="separator"></div>
            <div class="text">أرسل طلبك وسيتواصل فريق الطوارئ معك فوراً</div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="hms-ambulance-card">
                    <form id="hmsAmbulanceForm" method="POST" action="{{ route('ambulance.request') }}">@csrf
                        <div class="row">
                            <div class="col-md-6 form-group"><input type="text" name="patient_name" placeholder="الاسم" required></div>
                            <div class="col-md-6 form-group"><input type="tel" name="phone" placeholder="الهاتف" required></div>
                            <div class="col-12 form-group"><textarea name="address" placeholder="العنوان بالتفصيل" required rows="2"></textarea></div>
                            <div class="col-md-6 form-group">
                                <label>مستوى الأولوية (فرز)</label>
                                <select name="triage_level" class="form-control" required>
                                    <option value="critical">🔴 حرج — حالة خطيرة</option>
                                    <option value="urgent">🟠 عاجل</option>
                                    <option value="normal" selected>🟢 عادي</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group"><textarea name="notes" placeholder="ملاحظات (اختياري)" rows="2"></textarea></div>
                            <div class="col-12">
                                <button type="submit" class="theme-btn btn-style-two hms-ambulance-submit">
                                    <span class="txt"><i class="fas fa-ambulance"></i> طلب إسعاف فوري</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="hms-modal" id="hmsAmbulanceModal" aria-hidden="true">
    <div class="hms-modal__backdrop" data-hms-ambulance-close></div>
    <div class="hms-modal__dialog hms-modal__dialog--ambulance" role="dialog" aria-modal="true" aria-labelledby="hmsAmbulanceModalTitle">
        <button type="button" class="hms-modal__close" data-hms-ambulance-close aria-label="إغلاق">&times;</button>
        <div class="hms-modal__icon hms-modal__icon--ambulance" id="hmsAmbulanceModalIcon">
            <i class="fas fa-ambulance"></i>
        </div>
        <h3 id="hmsAmbulanceModalTitle">تم إرسال طلب الإسعاف</h3>
        <p id="hmsAmbulanceModalMessage">
            تم إرسال إشعار مستعجل للإدارة، وسيتم التواصل معك على رقم الهاتف المسجل.
        </p>
        <button type="button" class="theme-btn btn-style-two" data-hms-ambulance-close>
            <span class="txt">حسناً</span>
        </button>
    </div>
</div>

@push('scripts')
<script>
(function($){
    var $modal = $('#hmsAmbulanceModal');
    var defaultSuccess = 'تم إرسال إشعار مستعجل للإدارة، وسيتم التواصل معك على رقم الهاتف المسجل.';

    function parseResponse(raw) {
        if (window.hmsParseJson) {
            try { return window.hmsParseJson(raw) || {}; } catch (e) { return {}; }
        }
        try { return JSON.parse(String(raw).replace(/^\u0000+/, '').trim()) || {}; } catch (e) { return {}; }
    }

    function openAmbulanceModal(message, isError) {
        var $icon = $('#hmsAmbulanceModalIcon');
        var $title = $('#hmsAmbulanceModalTitle');

        if (isError) {
            $icon.removeClass('hms-modal__icon--ambulance').addClass('hms-modal__icon--error');
            $icon.html('<i class="fas fa-exclamation-circle"></i>');
            $title.text('تعذر إرسال الطلب');
        } else {
            $icon.removeClass('hms-modal__icon--error').addClass('hms-modal__icon--ambulance');
            $icon.html('<i class="fas fa-ambulance"></i>');
            $title.text('تم إرسال طلب الإسعاف');
        }

        $('#hmsAmbulanceModalMessage').text(message || defaultSuccess);
        $modal.addClass('is-open').attr('aria-hidden', 'false');
        $('body').addClass('hms-modal-open');
    }

    function closeAmbulanceModal() {
        $modal.removeClass('is-open').attr('aria-hidden', 'true');
        $('body').removeClass('hms-modal-open');
    }

    $(document).on('click', '[data-hms-ambulance-close]', closeAmbulanceModal);
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $modal.hasClass('is-open')) {
            closeAmbulanceModal();
        }
    });

    $('#hmsAmbulanceForm').on('submit', function(e){
        e.preventDefault();
        var $f = $(this);
        var $btn = $f.find('.hms-ambulance-submit');
        var $txt = $btn.find('.txt');
        var originalHtml = $txt.html();
        $btn.prop('disabled', true);
        $txt.html('<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...');

        $.ajax({
            url: $f.attr('action'),
            method: 'POST',
            data: $f.serialize(),
            dataType: 'text',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).done(function(raw){
            var res = parseResponse(raw);
            if (res.success === false || res.ok === false) {
                openAmbulanceModal(res.message || 'حدث خطأ أثناء إرسال الطلب، حاول مرة أخرى.', true);
                return;
            }
            openAmbulanceModal(res.message || defaultSuccess, false);
            $f[0].reset();
        }).fail(function(xhr){
            var res = parseResponse(xhr.responseText);
            var msg = res.message || 'حدث خطأ أثناء إرسال الطلب، حاول مرة أخرى.';
            if (res.errors) {
                var first = Object.values(res.errors)[0];
                if (first && first[0]) msg = first[0];
            }
            openAmbulanceModal(msg, true);
        }).always(function(){
            $btn.prop('disabled', false);
            $txt.html(originalHtml);
        });
    });
})(jQuery);
</script>
@endpush

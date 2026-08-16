<script src="/WebSite/js/jquery.js"></script>
<script src="/WebSite/js/popper.min.js"></script>
<script src="/WebSite/js/jquery-ui.js"></script>
<script src="/WebSite/js/bootstrap.min.js"></script>
<script src="/WebSite/js/jquery.fancybox.js"></script>
<script src="/WebSite/js/parallax.min.js"></script>
<script src="/WebSite/js/jquery.paroller.min.js"></script>
<script src="/WebSite/js/owl.js"></script>
<script src="/WebSite/js/wow.js"></script>
<script src="/WebSite/js/nav-tool.js"></script>
<script src="/WebSite/js/jquery.magnific-popup.min.js"></script>
<script src="/WebSite/js/main.js"></script>
<script src="/WebSite/js/swiper.min.js"></script>
<script src="/WebSite/js/appear.js"></script>
<script src="/WebSite/js/script.js"></script>

<script>
(function ($) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function hmsParseJson(raw) {
        if (raw == null || raw === '') {
            return null;
        }
        if (typeof raw === 'object') {
            return raw;
        }
        var cleaned = String(raw).replace(/^\u0000+/, '').trim();
        if (!cleaned) {
            return null;
        }
        var jsonStart = cleaned.search(/[\[{]/);
        if (jsonStart > 0) {
            cleaned = cleaned.slice(jsonStart);
        }
        return JSON.parse(cleaned);
    }

    function hmsAjaxJson(options) {
        var headers = $.extend({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }, options.headers || {});

        return $.ajax($.extend({}, options, {
            dataType: 'text',
            headers: headers
        })).then(function (raw) {
            try {
                return hmsParseJson(raw) || {};
            } catch (err) {
                return $.Deferred().reject({
                    status: 200,
                    responseText: raw,
                    parseError: true
                }).promise();
            }
        });
    }

    window.hmsParseJson = hmsParseJson;
    window.hmsAjaxJson = hmsAjaxJson;
    window.hmsRequirePatientAuth = requireAuth;
    window.hmsOpenAuthModal = openAuthModal;

    var pendingAction = null;

    function isPatientAuth() {
        return $('body').attr('data-patient-auth') === '1';
    }

    function setPatientAuth(flag) {
        $('body').attr('data-patient-auth', flag ? '1' : '0');
    }

    function openAuthModal(action) {
        pendingAction = action || null;
        $('#hmsAuthAlert').hide().text('');
        $('#hmsPatientAuthModal').addClass('is-open').attr('aria-hidden', 'false');
        $('body').addClass('hms-auth-open');
    }

    function closeAuthModal() {
        $('#hmsPatientAuthModal').removeClass('is-open').attr('aria-hidden', 'true');
        $('body').removeClass('hms-auth-open');
    }

    function showAuthAlert(msg, ok) {
        $('#hmsAuthAlert')
            .toggleClass('is-ok', !!ok)
            .toggleClass('is-error', !ok)
            .text(msg)
            .show();
    }

    function requireAuth(action) {
        if (isPatientAuth()) {
            if (typeof action === 'function') action();
            return true;
        }
        openAuthModal(action);
        return false;
    }

    $(document).on('click', '[data-auth-close]', function () {
        closeAuthModal();
        pendingAction = null;
    });

    $(document).on('click', '.hms-auth-tab', function () {
        var tab = $(this).data('auth-tab');
        $('.hms-auth-tab').removeClass('is-active');
        $(this).addClass('is-active');
        $('.hms-auth-form').removeClass('is-active');
        $('.hms-auth-form[data-auth-panel="' + tab + '"]').addClass('is-active');
        $('#hmsAuthAlert').hide();
    });

    function extractAuthError(xhr, fallback) {
        var data = xhr.responseJSON || null;
        if (!data && xhr.responseText) {
            try {
                data = hmsParseJson(xhr.responseText);
            } catch (err) {
                data = null;
            }
        }
        data = data || {};
        if (data.messages && data.messages.length) {
            return data.messages.join(' — ');
        }
        if (data.errors) {
            var list = [];
            Object.keys(data.errors).forEach(function (key) {
                (data.errors[key] || []).forEach(function (m) { list.push(m); });
            });
            if (list.length) return list.join(' — ');
        }
        if (data.message) return data.message;
        if (xhr.status === 419) return 'انتهت صلاحية الجلسة. حدّث الصفحة ثم أعد المحاولة.';
        if (xhr.status === 0) return 'تعذر الاتصال بالخادم. تحقق من الإنترنت.';
        return fallback;
    }

    function setAuthBusy($form, busy) {
        var $btn = $form.find('.hms-auth-submit');
        $btn.prop('disabled', !!busy).toggleClass('is-loading', !!busy);
        $btn.find('.txt').text(busy
            ? ($form.attr('id') === 'hmsPatientRegisterForm' ? 'جاري إنشاء الحساب...' : 'جاري الدخول...')
            : ($form.attr('id') === 'hmsPatientRegisterForm' ? 'إنشاء حساب' : 'دخول')
        );
    }

    $('#hmsPatientLoginForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        setAuthBusy($form, true);
        hmsAjaxJson({
            url: $('body').data('patient-login-url'),
            method: 'POST',
            data: $form.serialize()
        })
            .done(function (res) {
                if (res.ok === false || res.success === false) {
                    showAuthAlert(res.message || 'فشل تسجيل الدخول. تحقق من البيانات.', false);
                    return;
                }
                setPatientAuth(true);
                showAuthAlert(res.message || 'تم الدخول', true);
                setTimeout(function () {
                    closeAuthModal();
                    if (typeof pendingAction === 'function') {
                        pendingAction();
                        pendingAction = null;
                    } else {
                        window.location.reload();
                    }
                }, 400);
            })
            .fail(function (xhr) {
                showAuthAlert(extractAuthError(xhr, 'فشل تسجيل الدخول. تحقق من البيانات.'), false);
            })
            .always(function () {
                setAuthBusy($form, false);
            });
    });

    $('#hmsPatientRegisterForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        setAuthBusy($form, true);
        hmsAjaxJson({
            url: $('body').data('patient-register-url'),
            method: 'POST',
            data: $form.serialize()
        })
            .done(function (res) {
                if (res.ok === false || res.success === false) {
                    showAuthAlert(res.message || 'تعذر إنشاء الحساب. راجع البيانات المدخلة.', false);
                    return;
                }
                setPatientAuth(true);
                showAuthAlert(res.message || 'تم إنشاء الحساب', true);
                setTimeout(function () {
                    closeAuthModal();
                    if (typeof pendingAction === 'function') {
                        pendingAction();
                        pendingAction = null;
                    } else {
                        window.location.reload();
                    }
                }, 400);
            })
            .fail(function (xhr) {
                showAuthAlert(extractAuthError(xhr, 'تعذر إنشاء الحساب. راجع البيانات المدخلة.'), false);
            })
            .always(function () {
                setAuthBusy($form, false);
            });
    });
    function doLike($btn) {
        if ($btn.data('busy')) return;
        $btn.data('busy', true);
        hmsAjaxJson({
            url: $btn.data('like-url'),
            method: 'POST'
        })
            .done(function (res) {
                if (res.ok === false) {
                    if (res.auth_required) {
                        openAuthModal(function () { doLike($btn); });
                    }
                    return;
                }
                $btn.find('.js-likes').text(res.likes);
                $btn.toggleClass('is-liked', !!res.liked);
            })
            .fail(function (xhr) {
                var data = null;
                try {
                    data = hmsParseJson(xhr.responseText);
                } catch (err) {
                    data = null;
                }
                if (xhr.status === 401 || (data && data.auth_required)) {
                    openAuthModal(function () { doLike($btn); });
                }
            })
            .always(function () {
                $btn.data('busy', false);
            });
    }

    $(document).on('click', '.hms-like-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        requireAuth(function () { doLike($btn); });
    });

    $(document).on('click', '.hms-comment-open', function (e) {
        e.preventDefault();
        var url = $(this).data('comment-url');
        requireAuth(function () {
            if (url) window.location.href = url;
        });
    });

    $('#hmsCommentForm').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var url = $form.data('comment-url');
        var $btn = $form.find('button[type=submit]');
        requireAuth(function () {
            if ($form.data('busy')) return;
            $form.data('busy', true);
            $btn.prop('disabled', true);
            hmsAjaxJson({
                url: url,
                method: 'POST',
                data: $form.serialize()
            })
                .done(function (res) {
                    if (!res || (res.ok === false && res.success !== true) || !res.comment) {
                        alert((res && res.message) || 'تعذر إضافة التعليق');
                        return;
                    }
                    var author = res.comment.author || 'مريض';
                    var initial = res.comment.initial || author.charAt(0);
                    var avatar = res.comment.avatar || ($form.data('avatar') || '');
                    var html = '<div class="hms-fb-comment">' +
                        '<div class="hms-comment-avatar hms-comment-avatar--logo" title="' + $('<div>').text(author).html() + '">' +
                        '<img src="' + $('<div>').text(avatar).html() + '" alt="" onerror="this.style.display=\'none\'">' +
                        '<span class="hms-comment-avatar__fallback">' + $('<div>').text(initial).html() + '</span>' +
                        '</div>' +
                        '<div class="hms-fb-comment__main">' +
                        '<div class="hms-fb-bubble">' +
                        '<strong class="hms-fb-bubble__name">' + $('<div>').text(author).html() + '</strong>' +
                        '<p class="hms-fb-bubble__text">' + $('<div>').text(res.comment.body || '').html() + '</p>' +
                        '</div>' +
                        '<div class="hms-fb-meta"><span>' + $('<div>').text(res.comment.date || 'الآن').html() + '</span></div>' +
                        '</div></div>';
                    $('#hmsCommentsList').prepend(html);
                    $('#hmsCommentsEmpty').hide();
                    $form.find('[name=body]').val('');
                    if (typeof res.comments_count !== 'undefined') {
                        $('.js-comments-count').text(res.comments_count);
                    }
                })
                .fail(function (xhr) {
                    var data = null;
                    try {
                        data = hmsParseJson(xhr.responseText);
                    } catch (err) {
                        data = null;
                    }
                    if (xhr.status === 401 || (data && data.auth_required)) {
                        openAuthModal(null);
                        return;
                    }
                    var msg = (data && data.message) || 'تعذر إضافة التعليق';
                    if (data && data.errors) {
                        var first = Object.values(data.errors)[0];
                        if (first && first[0]) msg = first[0];
                    }
                    alert(msg);
                })
                .always(function () {
                    $form.data('busy', false);
                    $btn.prop('disabled', false);
                });
        });
    });

    // إخفاء زر عرض كل المقالات بعد الضغط
    $(document).on('click', '.hms-view-all-blogs', function () {
        var $wrap = $(this).closest('.hms-view-all-blogs-wrap');
        try { sessionStorage.setItem('hms_hide_view_blogs', '1'); } catch (err) {}
        $wrap.fadeOut(200);
    });

    try {
        if (sessionStorage.getItem('hms_hide_view_blogs') === '1') {
            $('.hms-view-all-blogs-wrap').hide();
        }
    } catch (err) {}
})(jQuery);
</script>

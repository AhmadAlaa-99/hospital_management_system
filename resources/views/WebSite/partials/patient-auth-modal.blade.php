{{-- مودال تسجيل/إنشاء حساب مريض للتفاعل مع المقالات --}}
<div class="hms-auth-modal" id="hmsPatientAuthModal" aria-hidden="true">
    <div class="hms-auth-modal__backdrop" data-auth-close></div>
    <div class="hms-auth-modal__dialog" role="dialog" aria-modal="true">
        <button type="button" class="hms-auth-modal__close" data-auth-close aria-label="إغلاق">&times;</button>
        <h3 class="hms-auth-modal__title">يلزم تسجيل الدخول</h3>
        <p class="hms-auth-modal__text">للإعجاب أو إضافة تعليق، سجّل دخولك كمريض أو أنشئ حساباً جديداً.</p>

        <div class="hms-auth-tabs">
            <button type="button" class="hms-auth-tab is-active" data-auth-tab="login">تسجيل الدخول</button>
            <button type="button" class="hms-auth-tab" data-auth-tab="register">إنشاء حساب</button>
        </div>

        <div class="hms-auth-alert" id="hmsAuthAlert" style="display:none"></div>

        <form id="hmsPatientLoginForm" class="hms-auth-form is-active" data-auth-panel="login">
            <label>البريد الإلكتروني
                <input type="email" name="email" required placeholder="patient@example.com">
            </label>
            <label>كلمة المرور
                <input type="password" name="password" required placeholder="••••••••">
            </label>
            <button type="submit" class="theme-btn btn-style-two hms-auth-submit"><span class="txt">دخول</span></button>
        </form>

        <form id="hmsPatientRegisterForm" class="hms-auth-form" data-auth-panel="register">
            <label>الاسم الكامل
                <input type="text" name="name" required placeholder="الاسم">
            </label>
            <label>البريد الإلكتروني
                <input type="email" name="email" required placeholder="patient@example.com">
            </label>
            <label>رقم الهاتف
                <input type="text" name="phone" required placeholder="09xxxxxxxx">
            </label>
            <label>كلمة المرور
                <input type="password" name="password" required minlength="6" placeholder="6 أحرف على الأقل">
            </label>
            <label>تأكيد كلمة المرور
                <input type="password" name="password_confirmation" required minlength="6">
            </label>
            <button type="submit" class="theme-btn btn-style-two hms-auth-submit"><span class="txt">إنشاء حساب</span></button>
        </form>
    </div>
</div>

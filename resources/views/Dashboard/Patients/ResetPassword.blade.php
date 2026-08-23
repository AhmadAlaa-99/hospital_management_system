<div class="modal fade" id="ResetPassword{{ $Patient->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعيين كلمة مرور جديدة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('Patients.reset-password', $Patient->id) }}" method="post">
                    @csrf
                    <p class="text-muted mb-3">أدخل كلمة المرور الجديدة للمريض. ستُعرض لك بعد الحفظ لتسليمها للمريض.</p>
                    <input type="text" class="form-control mb-3" readonly value="{{ $Patient->name }}">

                    <div class="form-group mb-0">
                        <label for="new-password-{{ $Patient->id }}">كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <input type="text"
                                   name="password"
                                   id="new-password-{{ $Patient->id }}"
                                   class="form-control"
                                   required
                                   minlength="8"
                                   maxlength="64"
                                   autocomplete="off"
                                   placeholder="8 أحرف على الأقل">
                            <div class="input-group-append">
                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        onclick="generatePatientPassword({{ $Patient->id }})">
                                    توليد تلقائي
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">الكلمة مرئية للمدير — انسخها وسلّمها للمريض.</small>
                    </div>

                    <div class="modal-footer px-0 pb-0 mt-3">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">حفظ كلمة المرور</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

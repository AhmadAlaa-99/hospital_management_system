<div class="modal fade" id="ResetPassword{{ $Patient->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إعادة تعيين كلمة المرور</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('Patients.reset-password', $Patient->id) }}" method="post">
                    @csrf
                    <p class="text-muted">سيتم تعيين كلمة المرور إلى <strong>رقم الهاتف</strong> المسجّل للمريض.</p>
                    <input type="text" class="form-control mb-2" readonly value="{{ $Patient->name }}">
                    <input type="text" class="form-control" readonly value="الهاتف: {{ $Patient->Phone ?: '— غير مسجل —' }}">
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning" @if(!$Patient->Phone) disabled @endif>تأكيد إعادة التعيين</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

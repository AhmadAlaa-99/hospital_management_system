<!-- Modal: تغيير حالة الطبيب -->
<div class="modal fade" id="update_status{{ $doctor->id }}" tabindex="-1" role="dialog" aria-labelledby="statusLabel{{ $doctor->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusLabel{{ $doctor->id }}">
                    {{ $doctor->status == 1 ? 'إلغاء تفعيل الطبيب' : 'تفعيل الطبيب' }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('update_status') }}" method="post" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">
                        الطبيب: <strong>{{ $doctor->name }}</strong><br>
                        الحالة الحالية:
                        <strong class="{{ $doctor->status == 1 ? 'text-success' : 'text-danger' }}">
                            {{ $doctor->status == 1 ? trans('doctors.Enabled') : trans('doctors.Not_enabled') }}
                        </strong>
                    </p>
                    <input type="hidden" name="id" value="{{ $doctor->id }}">
                    <input type="hidden" name="status" value="{{ $doctor->status == 1 ? 0 : 1 }}">
                    <div class="alert alert-{{ $doctor->status == 1 ? 'warning' : 'info' }} mb-0">
                        @if($doctor->status == 1)
                            سيتم إلغاء تفعيل الحساب ولن يظهر الطبيب في الحجوزات العامة.
                        @else
                            سيتم تفعيل الحساب ليظهر الطبيب في الحجوزات العامة.
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-{{ $doctor->status == 1 ? 'warning' : 'success' }}">
                        {{ $doctor->status == 1 ? 'تأكيد إلغاء التفعيل' : 'تأكيد التفعيل' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

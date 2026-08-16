<!-- Refusal appointment -->
<div class="modal fade" id="Refusal{{ $appointment->id }}" tabindex="-1" aria-labelledby="refusalLabel{{ $appointment->id }}"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refusalLabel{{ $appointment->id }}">رفض الموعد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('appointments.refuse', $appointment->id) }}" method="post">
                    @csrf
                    <p class="mg-b-20">هل أنت متأكد من رفض موعد المريض: <strong>{{ $appointment->name }}</strong>؟</p>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button class="btn btn-danger">تأكيد الرفض</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

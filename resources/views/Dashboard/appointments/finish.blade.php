<!-- Finish appointment -->
<div class="modal fade" id="Finish{{ $appointment->id }}" tabindex="-1" aria-labelledby="finishLabel{{ $appointment->id }}"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="finishLabel{{ $appointment->id }}">انهاء الموعد</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('appointments.destroy', $appointment->id) }}" method="post">
                    @method('DELETE')
                    @csrf
                    <p class="mg-b-20">هل أنت متأكد من انهاء موعد المريض: <strong>{{ $appointment->name }}</strong>؟</p>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button class="btn btn-warning">تأكيد الانهاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

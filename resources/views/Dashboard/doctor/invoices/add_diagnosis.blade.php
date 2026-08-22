<!-- Modal -->
<div class="modal fade" id="add_diagnosis{{$invoice->id}}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تشخيص ووصفة إلكترونية</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{route('Diagnostics.store')}}" method="POST">
            @csrf
            <div class="modal-body">
                <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                <input type="hidden" name="patient_id" value="{{$invoice->patient_id}}">
                <input type="hidden" name="doctor_id" value="{{$invoice->doctor_id}}">

                <div class="form-group">
                    <label>التشخيص</label>
                    <textarea class="form-control" name="diagnosis" rows="4" required></textarea>
                </div>

                <h6 class="mt-3">الوصفة الإلكترونية (e-Prescription)</h6>
                <div id="rx-rows-{{ $invoice->id }}">
                    <div class="row rx-row mb-2">
                        <div class="col-md-3"><input type="text" name="medicines[0][medicine_name]" class="form-control" placeholder="اسم الدواء"></div>
                        <div class="col-md-2"><input type="text" name="medicines[0][dosage]" class="form-control" placeholder="الجرعة"></div>
                        <div class="col-md-2"><input type="text" name="medicines[0][frequency]" class="form-control" placeholder="التكرار"></div>
                        <div class="col-md-2"><input type="number" name="medicines[0][duration_days]" class="form-control" placeholder="أيام"></div>
                        <div class="col-md-3"><input type="text" name="medicines[0][instructions]" class="form-control" placeholder="تعليمات"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRxRow({{ $invoice->id }})">+ دواء</button>

                <div class="form-group mt-3">
                    <label>ملاحظات أدوية (اختياري)</label>
                    <textarea class="form-control" name="medicine" rows="2" placeholder="ملاحظات عامة"></textarea>
                </div>

                <hr>
                <h6>خطة متابعة (اختياري)</h6>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>تاريخ المتابعة</label>
                        <input type="date" name="follow_up_date" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>ملاحظات المتابعة</label>
                        <input type="text" name="follow_up_notes" class="form-control" placeholder="مثال: مراجعة بعد أسبوعين">
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="create_follow_up_appointment" value="1" class="form-check-input" id="fua-{{ $invoice->id }}">
                    <label class="form-check-label" for="fua-{{ $invoice->id }}">إنشاء موعد متابعة تلقائياً</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                <button type="submit" class="btn btn-primary btn-hms-primary">حفظ التشخيص والوصفة</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
function addRxRow(invoiceId) {
    var container = document.getElementById('rx-rows-' + invoiceId);
    var idx = container.querySelectorAll('.rx-row').length;
    var row = document.createElement('div');
    row.className = 'row rx-row mb-2';
    row.innerHTML = '<div class="col-md-3"><input type="text" name="medicines['+idx+'][medicine_name]" class="form-control" placeholder="اسم الدواء"></div>' +
        '<div class="col-md-2"><input type="text" name="medicines['+idx+'][dosage]" class="form-control" placeholder="الجرعة"></div>' +
        '<div class="col-md-2"><input type="text" name="medicines['+idx+'][frequency]" class="form-control" placeholder="التكرار"></div>' +
        '<div class="col-md-2"><input type="number" name="medicines['+idx+'][duration_days]" class="form-control" placeholder="أيام"></div>' +
        '<div class="col-md-3"><input type="text" name="medicines['+idx+'][instructions]" class="form-control" placeholder="تعليمات"></div>';
    container.appendChild(row);
}
</script>

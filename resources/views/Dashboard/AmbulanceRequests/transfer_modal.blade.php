<div class="modal fade" id="transfer{{ $req->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ambulance-requests.transfer-clinic', $req) }}" method="POST">
                @csrf
                <div class="modal-header"><h5>تحويل لعيادة تخصصية</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>التخصص / القسم</label>
                        <select name="section_id" class="form-control" required>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الطبيب (اختياري)</label>
                        <select name="doctor_id" class="form-control">
                            <option value="">— تلقائي —</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات التحويل</label>
                        <textarea name="transfer_notes" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="create_appointment" value="1" class="form-check-input" id="appt{{ $req->id }}" checked>
                        <label class="form-check-label" for="appt{{ $req->id }}">إنشاء موعد عاجل للعيادة</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تحويل</button>
                </div>
            </form>
        </div>
    </div>
</div>

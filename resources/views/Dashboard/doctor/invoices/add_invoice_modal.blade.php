<div class="modal fade" id="doctorAddInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="doctorAddInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doctorAddInvoiceModalLabel">إضافة فاتورة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span>&times;</span></button>
            </div>
            <form action="{{ route('invoices.store') }}" method="POST" id="doctor-invoice-form">
                @csrf
                <input type="hidden" name="return_to" id="doctor_invoice_return_to" value="invoices">
                <input type="hidden" name="appointment_id" id="doctor_invoice_appointment_id" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>المريض <span class="text-danger">*</span></label>
                                <select name="patient_id" id="doctor_invoice_patient_id" class="form-control doctor-invoice-select2" required>
                                    <option value="">— اختر المريض —</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }} — {{ $patient->Phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>نوع الدفع <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="1">نقدي</option>
                                    <option value="2">اجل</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>القسم</label>
                                <input type="text" class="form-control" readonly value="{{ optional($doctor->section)->name }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border mb-3">
                                <div class="card-header py-2 bg-light">
                                    <i class="fas fa-file-medical ml-1"></i> خدمات مفردة
                                    <small class="text-muted">(يمكن اختيار أكثر من خدمة)</small>
                                </div>
                                <div class="card-body py-3">
                                    <select name="Service_ids[]" id="doctor_invoice_service_ids" class="form-control doctor-invoice-multi-select" multiple>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                                {{ $service->name }} — {{ number_format($service->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-2">كل خدمة تُنشئ فاتورة مستقلة — الخصم والضريبة تُحسب تلقائياً.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border mb-3">
                                <div class="card-header py-2 bg-light">
                                    <i class="fas fa-layer-group ml-1"></i> مجموعات خدمات
                                    <small class="text-muted">(يمكن اختيار أكثر من مجموعة)</small>
                                </div>
                                <div class="card-body py-3">
                                    <select name="Group_ids[]" id="doctor_invoice_group_ids" class="form-control doctor-invoice-multi-select" multiple>
                                        @foreach($groups as $group)
                                            <option value="{{ $group->id }}"
                                                    data-price="{{ $group->Total_before_discount }}"
                                                    data-discount="{{ $group->discount_value }}"
                                                    data-tax-rate="{{ $group->tax_rate }}"
                                                    data-total="{{ $group->Total_with_tax }}">
                                                {{ $group->name }} — {{ number_format($group->Total_with_tax, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-2">كل مجموعة تُنشئ فاتورة مستقلة بأسعار المجموعة المعرفة من الإدارة.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info small mb-0" id="doctor-invoice-preview">
                        <strong>ملخص:</strong> <span id="doctor-invoice-preview-text">لم يُختر أي خدمة أو مجموعة بعد.</span>
                    </div>

                    @if(isset($todayTickets) && $todayTickets->count())
                        <div class="alert alert-light border small mt-3 mb-0">
                            <strong>اختصار — مرضى اليوم:</strong>
                            @foreach($todayTickets as $ticket)
                                <button type="button"
                                        class="badge badge-info border-0 ml-1 doctor-invoice-prefill"
                                        data-patient-id="{{ $ticket->patient_id }}"
                                        data-appointment-id="{{ $ticket->appointment_id }}">
                                    {{ $ticket->ticket_number }} — {{ $ticket->patient_name }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-hms-primary">حفظ الفاتورة/الفواتير</button>
                </div>
            </form>
        </div>
    </div>
</div>

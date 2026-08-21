<div class="modal fade" id="doctorAddInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="doctorAddInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="doctorAddInvoiceModalLabel">إضافة فاتورة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق"><span>&times;</span></button>
            </div>
            <form action="{{ route('invoices.store') }}" method="POST" id="doctor-invoice-form">
                @csrf
                <input type="hidden" name="invoice_type" id="doctor_invoice_type" value="1">
                <input type="hidden" name="appointment_id" id="doctor_invoice_appointment_id" value="">

                <div class="modal-body">
                    <ul class="nav nav-tabs mb-3" id="doctorInvoiceTypeTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="single-tab" data-toggle="tab" href="#doctor-invoice-single" role="tab" data-invoice-type="1">
                                <i class="fas fa-file-medical ml-1"></i> خدمة مفردة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="group-tab" data-toggle="tab" href="#doctor-invoice-group" role="tab" data-invoice-type="2">
                                <i class="fas fa-layer-group ml-1"></i> مجموعة خدمات
                            </a>
                        </li>
                    </ul>

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

                    <div class="tab-content" id="doctorInvoiceTypeContent">
                        <div class="tab-pane fade show active" id="doctor-invoice-single" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>الخدمة</th>
                                        <th>السعر</th>
                                        <th>الخصم</th>
                                        <th>الضريبة %</th>
                                        <th>قيمة الضريبة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select name="Service_id" id="doctor_invoice_service_id" class="form-control">
                                                <option value="">— اختر الخدمة —</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service->id }}" data-price="{{ $service->price }}">
                                                        {{ $service->name }} — {{ number_format($service->price, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" id="doctor_single_price" class="form-control" readonly value="0.00"></td>
                                        <td><input type="text" id="doctor_single_discount" class="form-control" readonly value="0.00"></td>
                                        <td><input type="text" id="doctor_single_tax_rate" class="form-control" value="17" readonly></td>
                                        <td><input type="text" id="doctor_single_tax_value" class="form-control" readonly value="0.00"></td>
                                        <td><input type="text" id="doctor_single_total" class="form-control" readonly value="0.00"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted d-block mt-2">الخصم والضريبة تُحسب تلقائياً عند الحفظ (تأمين المريض إن وُجد).</small>
                        </div>

                        <div class="tab-pane fade" id="doctor-invoice-group" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered text-center mb-0">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>مجموعة الخدمات</th>
                                        <th>السعر</th>
                                        <th>الخصم</th>
                                        <th>الضريبة %</th>
                                        <th>قيمة الضريبة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <select name="Group_id" id="doctor_invoice_group_id" class="form-control" disabled>
                                                <option value="">— اختر المجموعة —</option>
                                                @foreach($groups as $group)
                                                    <option value="{{ $group->id }}"
                                                            data-price="{{ $group->Total_before_discount }}"
                                                            data-discount="{{ $group->discount_value }}"
                                                            data-tax-rate="{{ $group->tax_rate }}">
                                                        {{ $group->name }} — {{ number_format($group->Total_with_tax, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" id="doctor_group_price" class="form-control" readonly value="0.00"></td>
                                        <td><input type="text" id="doctor_group_discount" class="form-control" readonly value="0.00"></td>
                                        <td><input type="text" id="doctor_group_tax_rate" class="form-control" readonly value="0"></td>
                                        <td><input type="text" id="doctor_group_tax_value" class="form-control" readonly value="0.00"></td>
                                        <td><input type="text" id="doctor_group_total" class="form-control" readonly value="0.00"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted d-block mt-2">مجموعة الخدمات المعرّفة من الإدارة — أسعار وخصومات المجموعة تُطبَّق تلقائياً.</small>
                        </div>
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
                    <button type="submit" class="btn btn-primary btn-hms-primary">حفظ الفاتورة</button>
                </div>
            </form>
        </div>
    </div>
</div>

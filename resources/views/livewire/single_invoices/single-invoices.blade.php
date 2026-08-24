<div wire:key="single-invoices-panel-{{ $show_table ? 'list' : 'form' }}-{{ $updateMode ? (int) $single_invoice_id : 'new' }}">

    @if ($catchError)
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ $catchError }}
        </div>
    @endif

    @if ($InvoiceSaved)
        <div class="alert alert-info">تم حفظ البيانات بنجاح.</div>
    @endif

    @if ($InvoiceUpdated)
        <div class="alert alert-info">تم تعديل البيانات بنجاح.</div>
    @endif

    @if($show_table)
        @include('livewire.single_invoices.Table')
    @else
        <form wire:submit.prevent="store" autocomplete="off">
            <div class="row">
                <div class="col">
                    <label>اسم المريض</label>
                    <select wire:model="patient_id" wire:change="applyInsuranceDiscount" class="form-control" required>
                        <option value="">-- اختار من القائمة --</option>
                        @foreach($Patients as $Patient)
                            <option value="{{ $Patient->id }}">{{ $Patient->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>اسم الدكتور</label>
                    <select wire:model.lazy="doctor_id" class="form-control" required>
                        <option value="">-- اختار من القائمة --</option>
                        @foreach($Doctors as $Doctor)
                            <option value="{{ $Doctor->id }}">{{ $Doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>القسم</label>
                    <input wire:model="section_name" type="text" class="form-control" readonly placeholder="يظهر تلقائياً بعد اختيار الطبيب">
                </div>
                <div class="col">
                    <label>نوع الفاتورة</label>
                    <select wire:model="type" class="form-control" required>
                        <option value="">-- اختار من القائمة --</option>
                        <option value="1">نقدي</option>
                        <option value="2">اجل</option>
                    </select>
                </div>
            </div>
            <br>

            @if(!$updateMode)
                <div class="card border mb-3">
                    <div class="card-header py-2 bg-light">
                        <i class="fas fa-file-medical ml-1"></i> خدمات مفردة
                        <small class="text-muted">(يمكن اختيار أكثر من خدمة — كل خدمة تُنشئ فاتورة مستقلة)</small>
                    </div>
                    <div class="card-body py-3">
                        <div class="row">
                            @foreach($Services as $Service)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               wire:model="Service_ids"
                                               value="{{ $Service->id }}"
                                               id="service_cb_{{ $Service->id }}"
                                               class="custom-control-input">
                                        <label class="custom-control-label" for="service_cb_{{ $Service->id }}">
                                            {{ $Service->name }} — {{ number_format($Service->price, 2) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-2">الخصم والضريبة تُحسب تلقائياً لكل خدمة (بما فيها خصم التأمين إن وُجد).</small>
                        @if($insurance_note)
                            <small class="text-info d-block mt-2">{{ $insurance_note }}</small>
                        @endif
                    </div>
                </div>
            @else
                <div class="row row-sm">
                    <div class="col-xl-12">
                        <div class="card hms-form-card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped mg-b-0 text-md-nowrap" style="text-align: center">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>اسم الخدمة</th>
                                            <th>سعر الخدمة</th>
                                            <th>قيمة الخصم</th>
                                            <th>نسبة الضريبة</th>
                                            <th>قيمة الضريبة</th>
                                            <th>الاجمالي مع الضريبة</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <th scope="row">1</th>
                                            <td>
                                                <select wire:model="Service_id" class="form-control" wire:change="get_price">
                                                    <option value="">-- اختار الخدمة --</option>
                                                    @foreach($Services as $Service)
                                                        <option value="{{ $Service->id }}">{{ $Service->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input wire:model="price" type="text" class="form-control" readonly></td>
                                            <td><input wire:model="discount_value" type="text" class="form-control"></td>
                                            <th><input wire:model="tax_rate" type="text" class="form-control"></th>
                                            <td><input type="text" class="form-control" value="{{ $tax_value }}" readonly></td>
                                            <td><input type="text" class="form-control" readonly value="{{ $subtotal + $tax_value }}"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    @if($insurance_note)
                                        <small class="text-info d-block mt-2">{{ $insurance_note }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-3 d-flex" style="gap:10px">
                <input class="btn btn-success" type="submit" value="{{ $updateMode ? 'حفظ التعديل' : 'تأكيد البيانات' }}">
                <button type="button" class="btn btn-secondary" wire:click.prevent="show_form_table">رجوع للقائمة</button>
            </div>
        </form>
    @endif
</div>

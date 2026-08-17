<div >

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
        @include('livewire.group_invoices.Table')
    @else
        <form wire:submit.prevent="store" autocomplete="off">
            @csrf
            <div class="row">
                <div class="col">
                    <label>اسم المريض</label>
                    <select wire:model="patient_id" wire:change="applyInsuranceDiscount" class="form-control" required>
                        <option value=""  >-- اختار من القائمة --</option>
                        @foreach($Patients as $Patient)
                            <option value="{{$Patient->id}}">{{$Patient->name}}</option>
                        @endforeach
                    </select>
                </div>


                <div class="col">
                    <label>اسم الدكتور</label>
                    <select wire:model="doctor_id"  wire:change="get_section" class="form-control"  id="exampleFormControlSelect1" required>
                        <option value="" >-- اختار من القائمة --</option>
                        @foreach($Doctors as $Doctor)
                            <option value="{{$Doctor->id}}">{{$Doctor->name}}</option>
                        @endforeach
                    </select>
                </div>


                    <div class="col">
                        <label>القسم</label>
                        <input wire:model="section_name" type="text" class="form-control" readonly >
                    </div>

                <div class="col">
                    <label>نوع الفاتورة</label>
                    <select wire:model="type" class="form-control" {{$updateMode == true ? 'disabled':''}}>
                        <option value="" >-- اختار من القائمة --</option>
                        <option value="1">نقدي</option>
                        <option value="2">اجل</option>
                    </select>
                </div>


            </div><br>

            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class="card hms-form-card">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0"></h4>
                            </div>
                        </div>
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
                                            <select wire:model="Group_id" class="form-control" wire:change="get_price" id="exampleFormControlSelect1">
                                                <option value="">-- اختار الخدمة --</option>
                                                @foreach($Groups as $Group)
                                                    <option value="{{$Group->id}}">{{$Group->name}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input wire:model="price" type="text" class="form-control" readonly></td>
                                        <td><input wire:model="discount_value" type="text" class="form-control" readonly></td>
                                        <th><input wire:model="tax_rate" type="text" class="form-control" readonly ></th>
                                        <td><input type="text" class="form-control" value="{{$tax_value}}" readonly  ></td>
                                        <td><input type="text" class="form-control" readonly value="{{$subtotal + $tax_value }}"></td>
                                    </tr>
                                    </tbody>
                                </table>
                                @if($insurance_note)
                                    <small class="text-info d-block mt-2">{{ $insurance_note }}</small>
                                @endif
                            </div><!-- bd -->
                        </div><!-- bd -->
                    </div><!-- bd -->
                </div>
            </div>
            <div class="mt-3 d-flex" style="gap:10px">
                <input class="btn btn-success" type="submit" value="{{ $updateMode ? 'حفظ التعديل' : 'تأكيد البيانات' }}">
                <button type="button" class="btn btn-secondary" wire:click="show_form_table">رجوع للقائمة</button>
            </div>
        </form>
    @endif

</div>


<div wire:key="group-services-panel-{{ $show_table ? 'list' : 'form' }}-{{ $updateMode ? (int) $group_id : 'new' }}">

    @if ($catchError)
        <div class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ $catchError }}
        </div>
    @endif

    @if ($ServiceSaved)
        <div class="alert alert-info">تم حفظ البيانات بنجاح.</div>
    @endif

    @if ($ServiceUpdated)
        <div class="alert alert-info">تم تعديل البيانات بنجاح.</div>
    @endif

    @if($show_table)
        @include('livewire.GroupServices.index')
    @else
        <form wire:submit.prevent="saveGroup" autocomplete="off" onsubmit="return false;">
            @error('name_group') <div class="alert alert-warning py-2">{{ $message }}</div> @enderror
            @error('GroupsItems') <div class="alert alert-warning py-2">{{ $message }}</div> @enderror
            <div class="form-group">
                <label>اسم المجموعة <span class="text-danger">*</span></label>
                <input wire:model.defer="name_group" type="text" name="name_group" class="form-control @error('name_group') is-invalid @enderror" required>
            </div>

            <div class="form-group">
                <label>ملاحظات</label>
                <textarea wire:model.defer="notes" name="notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="card mt-4 hms-form-card">
                <div class="card-header">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-outline-primary btn-hms-primary"
                                wire:click.prevent="addService">اضافة خدمة فرعية
                        </button>
                        <small class="text-muted d-block mt-2">اختر الخدمة → اضغط «تأكيد» على السطر → ثم «تأكيد البيانات»</small>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered hms-table">
                            <thead>
                            <tr class="table-primary">
                                <th>اسم الخدمة</th>
                                <th width="200">العدد</th>
                                <th width="200">العمليات</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($GroupsItems as $index => $groupItem)
                                <tr wire:key="group-service-item-{{ $index }}-{{ $groupItem['service_id'] ?: 'new' }}">
                                    <td>
                                        @if($groupItem['is_saved'])
                                            <input type="hidden" name="GroupsItems[{{$index}}][service_id]"
                                                   wire:model="GroupsItems.{{$index}}.service_id"/>
                                            @if($groupItem['service_name'] && $groupItem['service_price'])
                                                {{ $groupItem['service_name'] }}
                                                ({{ number_format($groupItem['service_price'], 2) }})
                                            @endif
                                        @else
                                            <select name="GroupsItems[{{$index}}][service_id]"
                                                    class="form-control{{ $errors->has('GroupsItems.' . $index) ? ' is-invalid' : '' }}"
                                                    wire:model="GroupsItems.{{$index}}.service_id">
                                                <option value="">-- اختر الخدمة --</option>
                                                @foreach ($allServices as $service)
                                                    <option value="{{ $service->id }}">
                                                        {{ $service->name }}
                                                        ({{ number_format($service->price, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('GroupsItems.' . $index))
                                                <em class="invalid-feedback">
                                                    {{ $errors->first('GroupsItems.' . $index) }}
                                                </em>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($groupItem['is_saved'])
                                            <input type="hidden" name="GroupsItems[{{$index}}][quantity]"
                                                   wire:model="GroupsItems.{{$index}}.quantity"/>
                                            {{ $groupItem['quantity'] }}
                                        @else
                                            <input type="number" name="GroupsItems[{{$index}}][quantity]"
                                                   class="form-control" wire:model="GroupsItems.{{$index}}.quantity"/>
                                        @endif
                                    </td>
                                    <td>
                                        @if($groupItem['is_saved'])
                                            <button type="button" class="btn btn-sm btn-primary"
                                                    wire:click.prevent="editService({{$index}})">
                                                تعديل
                                            </button>
                                        @elseif($groupItem['service_id'])
                                            <button type="button" class="btn btn-sm btn-success mr-1"
                                                    wire:click.prevent="saveService({{$index}})">
                                                تأكيد
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger"
                                                wire:click.prevent="removeService({{$index}})">حذف
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>


                    <div class="col-lg-4 ml-auto text-right">
                        <table class="table pull-right">
                            <tr>
                                <td style="color: red">الاجمالي</td>
                                <td>{{ number_format($subtotal, 2) }}</td>
                            </tr>

                            <tr>
                                <td style="color: red">قيمة الخصم</td>
                                <td width="125">
                                    <input type="number" name="discount_value" class="form-control w-75 d-inline"
                                           wire:model="discount_value">
                                </td>
                            </tr>

                            <tr>
                                <td style="color: red">نسبة الضريبة</td>
                                <td>
                                    <input type="number" name="taxes" class="form-control w-75 d-inline" min="0"
                                           max="100" wire:model="taxes"> %
                                </td>
                            </tr>
                            <tr>
                                <td style="color: red">الاجمالي مع الضريبة</td>
                                <td>{{ number_format($total, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <br/>
                    <div class="hms-form-actions">
                        <button class="btn btn-primary btn-hms-primary" type="submit">تأكيد البيانات</button>
                        <button type="button" class="btn btn-secondary" wire:click.prevent="show_form_table">رجوع</button>
                    </div>
                </div>
            </div>

        </form>
    @endif


</div>


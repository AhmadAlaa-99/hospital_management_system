<button class="btn btn-primary pull-right mb-3" wire:click.prevent="show_form_add" type="button">
    <i class="fas fa-plus ml-1"></i> اضافة فاتورة جديدة
</button>
<div class="table-responsive hms-table-scroll-x">
    <table class="table text-md-nowrap hms-table hms-livewire-table" id="hms-group-invoices" style="text-align: center">
        <thead>
        <tr>
            <th>#</th>
            <th>اسم الخدمة</th>
            <th>اسم المريض</th>
            <th>تاريخ الفاتورة</th>
            <th>اسم الدكتور</th>
            <th>القسم</th>
            <th>سعر الخدمة</th>
            <th>قيمة الخصم</th>
            <th>نسبة الضريبة</th>
            <th>قيمة الضريبة</th>
            <th>الاجمالي مع الضريبة</th>
            <th>نوع الفاتورة</th>
            <th>العمليات</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($group_invoices as $group_invoice)
            <tr wire:key="ginvoice-row-{{ $group_invoice->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($group_invoice->Group)->name }}</td>
                <td>{{ optional($group_invoice->Patient)->name }}</td>
                <td>{{ $group_invoice->invoice_date }}</td>
                <td>{{ optional($group_invoice->Doctor)->name }}</td>
                <td>{{ optional($group_invoice->Section)->name }}</td>
                <td>{{ number_format($group_invoice->price, 2) }}</td>
                <td>{{ number_format($group_invoice->discount_value, 2) }}</td>
                <td>{{ $group_invoice->tax_rate }}%</td>
                <td>{{ number_format($group_invoice->tax_value, 2) }}</td>
                <td>{{ number_format($group_invoice->total_with_tax, 2) }}</td>
                <td>{{ $group_invoice->type == 1 ? 'نقدي' : 'اجل' }}</td>
                <td>
                    <div class="hms-actions">
                        <button type="button" wire:click.prevent="edit({{ $group_invoice->id }})" class="hms-action-btn hms-action-btn--edit" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="hms-action-btn hms-action-btn--delete" data-toggle="modal" data-target="#delete_invoice" wire:click.prevent="delete({{ $group_invoice->id }})" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                        <a href="{{ route('group_Print_single_invoices', $group_invoice->id) }}" target="_blank" class="hms-action-btn hms-action-btn--print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @include('livewire.group_invoices.delete')
</div>

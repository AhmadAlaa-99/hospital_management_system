<button class="btn btn-primary pull-right mb-3" wire:click.prevent="show_form_add" type="button">
    <i class="fas fa-plus ml-1"></i> اضافة فاتورة جديدة
</button>
<a href="{{ route('export.table', ['type' => 'invoices']) }}" class="btn btn-success pull-right mb-3 ml-2">
    <i class="fas fa-file-export ml-1"></i> تصدير
</a>
<div class="table-responsive hms-table-scroll-x">
    <table class="table text-md-nowrap hms-table hms-livewire-table" id="hms-single-invoices" style="text-align: center">
        <thead>
        <tr>
            <th>#</th>
            <th>رقم السجل</th>
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
        @foreach ($single_invoices as $single_invoice)
            <tr wire:key="invoice-row-{{ $single_invoice->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $single_invoice->id }}</td>
                <td>{{ optional($single_invoice->Service)->name }}</td>
                <td>{{ optional($single_invoice->Patient)->name }}</td>
                <td>{{ $single_invoice->invoice_date }}</td>
                <td>{{ optional($single_invoice->Doctor)->name }}</td>
                <td>{{ optional($single_invoice->Section)->name }}</td>
                <td>{{ number_format($single_invoice->price, 2) }}</td>
                <td>{{ number_format($single_invoice->discount_value, 2) }}</td>
                <td>{{ $single_invoice->tax_rate }}%</td>
                <td>{{ number_format($single_invoice->tax_value, 2) }}</td>
                <td>{{ number_format($single_invoice->total_with_tax, 2) }}</td>
                <td>{{ $single_invoice->type == 1 ? 'نقدي' : 'اجل' }}</td>
                <td>
                    <div class="hms-actions">
                        <button type="button" wire:click.prevent="edit({{ $single_invoice->id }})" class="hms-action-btn hms-action-btn--edit" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="hms-action-btn hms-action-btn--delete" data-toggle="modal" data-target="#delete_invoice" wire:click.prevent="delete({{ $single_invoice->id }})" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                        <a href="{{ route('Print_single_invoices', $single_invoice->id) }}" target="_blank" class="hms-action-btn hms-action-btn--print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @include('livewire.single_invoices.delete')
</div>

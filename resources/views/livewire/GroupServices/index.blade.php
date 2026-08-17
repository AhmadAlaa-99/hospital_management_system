<button class="btn btn-primary btn-hms-primary pull-right" wire:click.prevent="show_form_add" type="button">اضافة مجموعة خدمات</button><br><br>
<div class="table-responsive hms-table-scroll-x">
        <table class="table text-md-nowrap hms-table" id="hms-group-services-table" data-page-length="50" style="text-align: center">
        <thead>
            <tr>
                <th>#</th>
                <th>الاسم</th>
                <th>اجمالي العرض شامل الضريبة</th>
                <th>الملاحظات</th>
                <th class="text-center">العمليات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groups as $group)
                <tr wire:key="group-row-{{ $group->id }}">
                    <td>{{ $loop->iteration}}</td>
                    <td>{{ $group->name }}</td>
                    <td>{{ number_format($group->Total_with_tax, 2) }}</td>
                    <td>{{ $group->notes }}</td>
                    <td class="text-center">
                        <div class="hms-actions">
                            <button type="button" wire:click.prevent="edit({{ $group->id }})" class="hms-action-btn hms-action-btn--edit" title="تعديل">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="hms-action-btn hms-action-btn--delete" data-toggle="modal" data-target="#deleteGroup{{$group->id}}" title="حذف">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
              @include('livewire.GroupServices.delete')
            @endforeach
        </tbody>
    </table>
</div>

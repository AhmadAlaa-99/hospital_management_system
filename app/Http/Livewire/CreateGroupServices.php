<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateGroupServices extends Component
{
    public $GroupsItems = [];
    public $allServices = [];
    public $discount_value = 0;
    public $taxes = 17;
    public $name_group;
    public $notes;
    public $ServiceSaved = false;
    public $ServiceUpdated = false;
    public $show_table = true;
    public $updateMode = false;
    public $group_id;
    public $catchError;

    public function mount()
    {
        $this->allServices = Service::all();
    }

    public function render()
    {

        $total = 0;
        foreach ($this->GroupsItems as $groupItem) {
            if ($groupItem['is_saved'] && $groupItem['service_price'] && $groupItem['quantity']) {
                $total += $groupItem['service_price'] * $groupItem['quantity'];
            }
        }

        return view('livewire.GroupServices.create-group-services', [
            'groups'=>Group::all(),
            'subtotal' => $Total_after_discount = $total - ((is_numeric($this->discount_value) ? $this->discount_value : 0)),
            'total' => $Total_after_discount * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100)
        ]);

    }


    public function addService()
    {
        foreach ($this->GroupsItems as $key => $groupItem) {
            if (!$groupItem['is_saved']) {
                $this->addError('GroupsItems.' . $key, 'يجب حفظ هذا الخدمة قبل إنشاء خدمة جديدة.');
                return;
            }
        }

        $this->GroupsItems[] = [
            'service_id' => '',
            'quantity' => 1,
            'is_saved' => false,
            'service_name' => '',
            'service_price' => 0
        ];

        $this->ServiceSaved = false;
    }

    public function editService($index)
    {
        foreach ($this->GroupsItems as $key => $groupItem) {
            if (!$groupItem['is_saved']) {
                $this->addError('GroupsItems.' . $key, 'This line must be saved before editing another.');
                return;
            }
        }

        $this->GroupsItems[$index]['is_saved'] = false;
    }


    public function saveService($index)
    {
        $this->resetErrorBag();

        if (empty($this->GroupsItems[$index]['service_id'])) {
            $this->addError('GroupsItems.' . $index, 'يرجى اختيار الخدمة أولاً.');
            return;
        }

        $product = $this->allServices->find($this->GroupsItems[$index]['service_id']);
        if (!$product) {
            $this->addError('GroupsItems.' . $index, 'الخدمة المختارة غير موجودة.');
            return;
        }

        $this->GroupsItems[$index]['service_name'] = $product->name;
        $this->GroupsItems[$index]['service_price'] = $product->price;
        $this->GroupsItems[$index]['is_saved'] = true;
    }

    public function removeService($index)
    {
        unset($this->GroupsItems[$index]);
        $this->GroupsItems = array_values($this->GroupsItems);
    }

    public function saveGroup()
    {
        $this->resetErrorBag();
        $this->catchError = null;

        if (empty(trim((string) $this->name_group))) {
            $this->addError('name_group', 'اسم المجموعة مطلوب.');
            return;
        }

        $savedItems = array_filter($this->GroupsItems, fn ($item) => !empty($item['is_saved']));
        if (count($savedItems) === 0) {
            $this->addError('GroupsItems', 'أضف خدمة فرعية واحدة على الأقل واحفظها.');
            return;
        }

        try {
            DB::beginTransaction();

            if ($this->updateMode) {
                $Groups = Group::findOrFail($this->group_id);
            } else {
                $Groups = new Group();
            }

            $total = 0;
            foreach ($this->GroupsItems as $groupItem) {
                if ($groupItem['is_saved'] && $groupItem['service_price'] && $groupItem['quantity']) {
                    $total += $groupItem['service_price'] * $groupItem['quantity'];
                }
            }

            $Groups->Total_before_discount = $total;
            $Groups->discount_value = $this->discount_value;
            $Groups->Total_after_discount = $total - ((is_numeric($this->discount_value) ? $this->discount_value : 0));
            $Groups->tax_rate = $this->taxes;
            $Groups->Total_with_tax = $Groups->Total_after_discount * (1 + (is_numeric($this->taxes) ? $this->taxes : 0) / 100);
            $Groups->save();

            $Groups->name = $this->name_group;
            $Groups->notes = $this->notes;
            $Groups->save();

            $Groups->service_group()->detach();
            foreach ($this->GroupsItems as $GroupsItem) {
                if (!empty($GroupsItem['is_saved']) && !empty($GroupsItem['service_id'])) {
                    $Groups->service_group()->attach($GroupsItem['service_id'], ['quantity' => $GroupsItem['quantity']]);
                }
            }

            DB::commit();

            if ($this->updateMode) {
                $this->ServiceUpdated = true;
                $this->ServiceSaved = false;
            } else {
                $this->ServiceSaved = true;
                $this->ServiceUpdated = false;
            }

            $this->show_table = true;
            $this->updateMode = false;
            $this->reset('GroupsItems', 'name_group', 'notes', 'group_id');
            $this->discount_value = 0;
            $this->taxes = 17;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->catchError = \App\Helpers\FriendlyError::message($e->getMessage());
        }
    }

    public function show_form_add()
    {
        $this->show_table = false;
        $this->updateMode = false;
        $this->ServiceSaved = false;
        $this->ServiceUpdated = false;
        $this->catchError = null;
        $this->reset('GroupsItems', 'name_group', 'notes', 'group_id');
        $this->discount_value = 0;
        $this->taxes = 17;
    }

    public function show_form_table()
    {
        $this->show_table = true;
        $this->updateMode = false;
        $this->catchError = null;
    }

    public function edit($id)
    {
        $this->show_table = false;
        $this->updateMode = true;
        $this->catchError = null;
        $group = Group::with('service_group')->findOrFail($id);
        $this->group_id = $id;

        $this->reset('GroupsItems', 'name_group', 'notes');
        $this->name_group = $group->name;
        $this->notes = $group->notes;
        $this->discount_value = intval($group->discount_value);
        $this->taxes = $group->tax_rate ?: 17;
        $this->ServiceSaved = false;
        $this->ServiceUpdated = false;

        foreach ($group->service_group as $serviceGroup) {
            $this->GroupsItems[] = [
                'service_id' => $serviceGroup->id,
                'quantity' => $serviceGroup->pivot->quantity,
                'is_saved' => true,
                'service_name' => $serviceGroup->name,
                'service_price' => $serviceGroup->price
            ];
        }
    }

    public function delete($id)
    {
        try {
            Group::destroy($id);
            $this->show_table = true;
            $this->ServiceSaved = false;
            $this->ServiceUpdated = false;
            $this->catchError = null;
            $this->dispatchBrowserEvent('hms-close-modal', ['modalId' => 'deleteGroup' . $id]);
        } catch (\Exception $e) {
            $this->catchError = \App\Helpers\FriendlyError::message($e->getMessage());
        }
    }
}

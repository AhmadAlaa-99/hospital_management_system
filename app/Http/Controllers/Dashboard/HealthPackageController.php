<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\PatientPackageUsage;
use App\Models\Service;
use Illuminate\Http\Request;

class HealthPackageController extends Controller
{
    public function index()
    {
        $packages = Group::with('service_group')
            ->where('is_health_package', true)
            ->latest()
            ->paginate(20);

        $usages = PatientPackageUsage::with(['patient', 'group', 'service'])
            ->latest()
            ->limit(20)
            ->get();

        return view('Dashboard.HealthPackages.index', compact('packages', 'usages'));
    }

    public function update(Request $request, Group $group)
    {
        $data = $request->validate([
            'is_health_package' => 'required|boolean',
            'package_type' => 'nullable|string|max:100',
            'validity_days' => 'required|integer|min:1|max:365',
        ]);

        $group->update($data);

        session()->flash('edit');
        return back();
    }

    public function activateForPatient(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'group_id' => 'required|exists:groups,id',
            'invoice_id' => 'nullable|exists:invoices,id',
        ]);

        $group = Group::with('service_group')->findOrFail($data['group_id']);

        if (!$group->is_health_package) {
            return back()->withErrors(['error' => 'هذه المجموعة ليست باقة فحص.']);
        }

        foreach ($group->service_group as $service) {
            PatientPackageUsage::create([
                'patient_id' => $data['patient_id'],
                'group_id' => $group->id,
                'service_id' => $service->id,
                'invoice_id' => $data['invoice_id'] ?? null,
                'quantity_allowed' => $service->pivot->quantity ?? 1,
                'quantity_used' => 0,
                'expires_at' => now()->addDays($group->validity_days ?? 90),
            ]);
        }

        session()->flash('add');
        return back();
    }
}

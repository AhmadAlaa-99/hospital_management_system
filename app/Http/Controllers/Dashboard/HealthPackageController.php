<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Patient;
use App\Models\PatientPackageUsage;
use App\Helpers\FriendlyError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthPackageController extends Controller
{
    public function index()
    {
        $packages = Group::with('service_group')
            ->where('is_health_package', true)
            ->latest()
            ->get();

        $allGroups = Group::with('service_group')->orderBy('id')->get();
        $patients = Patient::orderBy('id')->get();

        $usages = PatientPackageUsage::with(['patient', 'group', 'service'])
            ->latest()
            ->limit(30)
            ->get();

        return view('Dashboard.HealthPackages.index', compact('packages', 'allGroups', 'patients', 'usages'));
    }

    public function markPackage(Request $request)
    {
        $data = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'is_health_package' => 'required|in:0,1',
            'package_type' => 'nullable|string|max:100',
            'validity_days' => 'nullable|integer|min:1|max:365',
        ], [
            'group_id.required' => 'يرجى اختيار المجموعة.',
        ]);

        $group = Group::findOrFail($data['group_id']);
        $group->update([
            'is_health_package' => (bool) $data['is_health_package'],
            'package_type' => $data['package_type'] ?? $group->package_type,
            'validity_days' => $data['validity_days'] ?? $group->validity_days ?? 90,
        ]);

        session()->flash('edit');
        return back();
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
        ], [
            'patient_id.required' => 'يرجى اختيار المريض.',
            'group_id.required' => 'يرجى اختيار الباقة.',
        ]);

        $group = Group::with('service_group')->findOrFail($data['group_id']);

        if (!$group->is_health_package) {
            return back()->withInput()->withErrors(['error' => 'هذه المجموعة ليست باقة فحص. فعّلها كباقة من القسم أعلاه أولاً.']);
        }

        if ($group->service_group->isEmpty()) {
            return back()->withInput()->withErrors(['error' => 'الباقة لا تحتوي خدمات. أضف خدمات للمجموعة من صفحة «مجموعة الخدمات» أولاً.']);
        }

        try {
            DB::transaction(function () use ($data, $group) {
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
            });

            session()->flash('add');
            return back();
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'error' => FriendlyError::message($e->getMessage()),
            ]);
        }
    }
}

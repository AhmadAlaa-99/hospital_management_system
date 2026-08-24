<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function edit()
    {
        $setting = SiteSetting::current();
        return view('Dashboard.site_settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = SiteSetting::current();

        if ($request->boolean('sham_cash_section')) {
            return $this->updateShamCash($request, $setting);
        }

        $request->validate([
            'hospital_name' => 'required|string|max:150',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'phone2' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'working_hours' => 'nullable|string|max:150',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:50',
            'about' => 'nullable|string|max:1000',
            'copyright' => 'nullable|string|max:255',
        ]);

        $setting->update($request->only([
            'hospital_name', 'address', 'city', 'phone', 'phone2', 'email',
            'working_hours', 'facebook', 'twitter', 'instagram', 'linkedin',
            'whatsapp', 'about', 'copyright',
        ]));

        session()->flash('edit');
        return back();
    }

    protected function updateShamCash(Request $request, SiteSetting $setting)
    {
        $request->validate([
            'sham_cash_wallet' => 'nullable|string|max:100',
            'sham_cash_instructions' => 'nullable|string|max:1000',
            'sham_cash_qr' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'sham_cash_enabled' => $request->boolean('sham_cash_enabled'),
            'sham_cash_wallet' => $request->sham_cash_wallet,
            'sham_cash_instructions' => $request->sham_cash_instructions,
        ];

        if ($request->hasFile('sham_cash_qr')) {
            if ($setting->sham_cash_qr_path) {
                Storage::disk('public')->delete($setting->sham_cash_qr_path);
            }
            $data['sham_cash_qr_path'] = $request->file('sham_cash_qr')->store('sham-cash', 'public');
        }

        $setting->update($data);
        AuditLogService::log('sham_cash_settings_updated', $setting);

        session()->flash('edit');
        return redirect()->route('site-settings.edit', ['tab' => 'sham-cash']);
    }
}

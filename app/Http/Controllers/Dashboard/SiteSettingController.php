<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SiteSettingController extends Controller
{
    public function edit()
    {
        $setting = SiteSetting::current();
        return view('Dashboard.site_settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
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

        $setting = SiteSetting::current();
        $setting->update($request->only($setting->getFillable()));

        session()->flash('edit');
        return back();
    }
}

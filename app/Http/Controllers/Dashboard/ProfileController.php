<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected function guardName(): string
    {
        foreach (['admin', 'doctor', 'patient', 'ray_employee', 'laboratorie_employee', 'web'] as $guard) {
            if (Auth::guard($guard)->check()) {
                return $guard;
            }
        }

        return 'web';
    }

    protected function user()
    {
        return Auth::guard($this->guardName())->user();
    }

    public function show()
    {
        $user = $this->user();
        $guard = $this->guardName();

        return view('Dashboard.profile.show', compact('user', 'guard'));
    }

    public function edit()
    {
        $user = $this->user();
        $guard = $this->guardName();

        return view('Dashboard.profile.edit', compact('user', 'guard'));
    }

    public function update(Request $request)
    {
        $user = $this->user();

        $request->validate([
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|max:150',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        session()->flash('edit');
        return redirect()->route('profile.show');
    }
}

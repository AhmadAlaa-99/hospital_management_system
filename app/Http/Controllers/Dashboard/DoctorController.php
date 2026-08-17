<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Interfaces\Doctors\DoctorRepositoryInterface;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{

    private $Doctors;

    public function __construct(DoctorRepositoryInterface $Doctors)
    {
        $this->Doctors = $Doctors;
    }


    public function index()
    {
        return $this->Doctors->index();
    }


    public function create()
    {
        return $this->Doctors->create();
    }


    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:doctors,email',
            'password' => 'required|min:6',
            'phone' => 'required|numeric|unique:doctors,phone',
            'name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'photo' => 'nullable|image|max:4096',
        ], [
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً.',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقاً.',
        ]);

        return $this->Doctors->store($request);
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        return $this->Doctors->edit($id);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:doctors,id',
            'email' => 'required|email|unique:doctors,email,' . $request->id,
            'phone' => 'required|numeric|unique:doctors,phone,' . $request->id,
            'name' => 'required|string|max:255',
            'section_id' => 'required|exists:sections,id',
            'photo' => 'nullable|image|max:4096',
        ], [
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً.',
            'phone.unique' => 'رقم الهاتف مستخدم مسبقاً.',
        ]);

        return $this->Doctors->update($request);
    }


    public function destroy(Request $request)
    {
        return $this->Doctors->destroy($request);
    }

    public function update_password(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'
        ]);

        return $this->Doctors->update_password($request);
    }

    public function update_status(Request $request)
    {
        $this->validate($request, [
            'status' => 'required|in:0,1',
        ]);
        return $this->Doctors->update_status($request);
    }
}

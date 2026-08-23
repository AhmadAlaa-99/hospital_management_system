<?php


namespace App\Repository\Patients;
use App\Interfaces\Patients\PatientRepositoryInterface;
use App\Models\Insurance;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientAccount;
use App\Models\ReceiptAccount;
use App\Models\single_invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PatientRepository implements PatientRepositoryInterface
{
   public function index()
   {
       $Patients = Patient::with('translations')->latest('id')->get();
       return view('Dashboard.Patients.index',compact('Patients'));
   }

    public function Show($id)
    {
        $Patient = patient::findorfail($id);
        $invoices = Invoice::where('patient_id', $id)->get();
        $receipt_accounts = ReceiptAccount::where('patient_id', $id)->get();
        $Patient_accounts = PatientAccount::where('patient_id', $id)->get();

        return view('Dashboard.Patients.show', compact('Patient', 'invoices', 'receipt_accounts', 'Patient_accounts'));
    }

    public function create()
   {
       $insurances = Insurance::where('status', 1)->get();
       return view('Dashboard.Patients.create', compact('insurances'));
   }

   public function store($request)
   {
       try {
           $Patients = new Patient();
           $Patients->email = $request->email;
           $Patients->Password = Hash::make($request->Phone);
           $Patients->Date_Birth = $request->Date_Birth;
           $Patients->Phone = $request->Phone;
           $Patients->Gender = $request->Gender;
           $Patients->Blood_Group = $request->Blood_Group;
           $Patients->insurance_id = $request->insurance_id ?: null;
           $Patients->save();
           //insert trans
           $Patients->name = $request->name;
           $Patients->Address = $request->Address;
           $Patients->save();
           session()->flash('add');
           return redirect()->route('Patients.index');
       }

       catch (\Exception $e) {
           return redirect()->back()->withErrors(['error' => $e->getMessage()]);
       }
   }

   public function edit($id)
   {
       $Patient = Patient::findorfail($id);
       $insurances = Insurance::where('status', 1)->get();
       return view('Dashboard.Patients.edit', compact('Patient', 'insurances'));
   }
   public function update($request)
   {
       $Patient = Patient::findOrFail($request->id);
       $Patient->email = $request->email;
       $Patient->Password = Hash::make($request->Phone);
       $Patient->Date_Birth = $request->Date_Birth;
       $Patient->Phone = $request->Phone;
       $Patient->Gender = $request->Gender;
       $Patient->Blood_Group = $request->Blood_Group;
       $Patient->insurance_id = $request->insurance_id ?: null;
       $Patient->save();
       // insert trans
       $Patient->name = $request->name;
       $Patient->Address = $request->Address;
       $Patient->save();
       session()->flash('edit');
       return redirect()->route('Patients.index');
   }

   public function destroy($request)
   {
       Patient ::destroy($request->id);
       session()->flash('delete');
       return redirect()->back();
   }

   public function resetPassword(Patient $patient, $request)
   {
       $request->validate([
           'password' => 'required|string|min:8|max:64',
       ], [
           'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
           'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
       ]);

       $plainPassword = $request->password;
       $patient->Password = Hash::make($plainPassword);
       $patient->save();

       session()->flash('edit');

       return redirect()->back()
           ->with('password_reset', $plainPassword)
           ->with('password_reset_patient', $patient->name);
   }
}

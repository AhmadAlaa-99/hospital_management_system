<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\AmbulanceRequest;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InsuranceClaimController extends Controller
{
    public function index()
    {
        $claims = InsuranceClaim::with(['patient', 'insurance', 'invoice'])->latest()->paginate(20);
        return view('Dashboard.InsuranceClaims.index', compact('claims'));
    }

    public function updateStatus(Request $request, InsuranceClaim $claim)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected,paid']);
        $claim->update(['status' => $request->status, 'notes' => $request->notes]);
        session()->flash('edit');
        return back();
    }

    public function report(Request $request)
    {
        $insuranceId = $request->get('insurance_id');
        $query = InsuranceClaim::with(['patient', 'insurance'])->latest();

        if ($insuranceId) {
            $query->where('insurance_id', $insuranceId);
        }

        $claims = $query->get();
        $insurances = Insurance::all();

        $summary = [
            'total' => $claims->sum('total_amount'),
            'company' => $claims->sum('company_amount'),
            'patient' => $claims->sum('patient_amount'),
            'count' => $claims->count(),
        ];

        return view('Dashboard.InsuranceClaims.report', compact('claims', 'insurances', 'summary', 'insuranceId'));
    }
}

<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\PharmacyInvoice;
use Illuminate\Support\Facades\Auth;

class PharmacyInvoiceController extends Controller
{
    public function index()
    {
        $invoices = PharmacyInvoice::with(['doctor', 'diagnostic'])
            ->where('patient_id', Auth::guard('patient')->id())
            ->latest()
            ->paginate(15);

        return view('Dashboard.dashboard_patient.pharmacy.invoices', compact('invoices'));
    }

    public function show(PharmacyInvoice $pharmacyInvoice)
    {
        if ($pharmacyInvoice->patient_id !== Auth::guard('patient')->id()) {
            abort(403);
        }

        $pharmacyInvoice->load(['items.medicine', 'items.prescription', 'doctor', 'diagnostic']);

        return view('Dashboard.dashboard_patient.pharmacy.show', compact('pharmacyInvoice'));
    }
}

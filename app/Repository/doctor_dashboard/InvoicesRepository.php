<?php

namespace App\Repository\doctor_dashboard;
use App\Interfaces\doctor_dashboard\InvoicesRepositoryInterface;
use App\Models\Invoice;
use App\Models\Laboratorie;
use App\Models\Ray;
use App\Support\DoctorInvoiceFormData;
use Illuminate\Support\Facades\Auth;

class InvoicesRepository implements InvoicesRepositoryInterface
{
    private function doctorId(): int
    {
        return (int) Auth::guard('doctor')->id();
    }

    // قائمة الكشوفات تحت الاجراء
    public function index()
    {
        $doctor = Auth::guard('doctor')->user();
        $invoices = Invoice::with(['Patient', 'Service', 'Group'])
            ->where('doctor_id', $this->doctorId())
            ->where('invoice_status', 1)
            ->get();

        $invoiceForm = DoctorInvoiceFormData::forDoctor($doctor);

        return view('Dashboard.doctor.invoices.index', compact('invoices') + $invoiceForm);
    }

    // قائمة المراجعات
    public function reviewInvoices()
    {
        $invoices = Invoice::with(['Patient', 'Service', 'Group'])
            ->where('doctor_id', $this->doctorId())
            ->where('invoice_status', 2)
            ->get();

        return view('Dashboard.doctor.invoices.review_invoices', compact('invoices'));
    }

    // قائمة الفواتير المكتملة
    public function completedInvoices()
    {
        $invoices = Invoice::with(['Patient', 'Service', 'Group'])
            ->where('doctor_id', $this->doctorId())
            ->where('invoice_status', 3)
            ->get();

        return view('Dashboard.doctor.invoices.completed_invoices', compact('invoices'));
    }

    public function show($id)
    {
        $rays = Ray::findorFail($id);
        if ($rays->doctor_id != $this->doctorId()) {
            //abort(404);
            return redirect()->route('404');
        }
        return view('Dashboard.doctor.invoices.view_rays', compact('rays'));
    }

    public function showLaboratorie($id)
    {
        $laboratories = Laboratorie::findorFail($id);
        if ($laboratories->doctor_id != $this->doctorId()) {
            //abort(404);
            return redirect()->route('404');
        }
        return view('Dashboard.doctor.invoices.view_laboratories', compact('laboratories'));
    }
}

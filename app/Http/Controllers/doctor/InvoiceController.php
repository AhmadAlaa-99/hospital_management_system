<?php

namespace App\Http\Controllers\doctor;

use App\Http\Controllers\Controller;
use App\Interfaces\doctor_dashboard\InvoicesRepositoryInterface;
use App\Models\Appointment;
use App\Services\ConsultationInvoiceService;
use App\Support\DoctorInvoiceFormData;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private $invoices;

    public function __construct(InvoicesRepositoryInterface $invoices)
    {
        $this->invoices = $invoices;
    }

    public function index()
    {
        return $this->invoices->index();
    }

    public function reviewInvoices()
    {
        return $this->invoices->reviewInvoices();
    }

    public function completedInvoices()
    {
        return $this->invoices->completedInvoices();
    }

    public function create(Request $request)
    {
        return redirect()->route('invoices.index', $request->only(['patient_id', 'appointment_id', 'Service_id']));
    }

    public function store(Request $request, ConsultationInvoiceService $billing)
    {
        $doctor = auth('doctor')->user();

        $data = $request->validate([
            'invoice_type' => 'required|in:1,2',
            'patient_id' => 'required|exists:patients,id',
            'Service_id' => 'required_if:invoice_type,1|nullable|exists:Services,id',
            'Group_id' => 'required_if:invoice_type,2|nullable|exists:groups,id',
            'type' => 'required|in:1,2',
            'appointment_id' => 'nullable|exists:appointments,id',
        ], [
            'patient_id.required' => 'يرجى اختيار المريض.',
            'Service_id.required_if' => 'يرجى اختيار الخدمة.',
            'Group_id.required_if' => 'يرجى اختيار مجموعة الخدمات.',
            'type.required' => 'يرجى اختيار نوع الدفع.',
        ]);

        if (!empty($data['appointment_id'])) {
            $appointment = Appointment::findOrFail($data['appointment_id']);
            if ((int) $appointment->doctor_id !== (int) $doctor->id) {
                return back()->withErrors(['error' => 'الموعد لا يخص هذا الطبيب.'])->withInput();
            }
        }

        if (!$doctor->section_id) {
            return back()->withErrors(['error' => 'الطبيب غير مربوط بقسم.'])->withInput();
        }

        $payload = [
            'patient_id' => $data['patient_id'],
            'doctor_id' => $doctor->id,
            'section_id' => $doctor->section_id,
            'type' => $data['type'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'invoice_status' => 1,
        ];

        if ((int) $data['invoice_type'] === 2) {
            $billing->createGroupInvoice(array_merge($payload, [
                'Group_id' => $data['Group_id'],
            ]));
            session()->flash('add', 'تم إنشاء فاتورة مجموعة الخدمات وستظهر في قائمة الكشوفات.');
        } else {
            $billing->createServiceInvoice(array_merge($payload, [
                'Service_id' => $data['Service_id'],
            ]));
            session()->flash('add', 'تم إنشاء فاتورة الخدمة وستظهر في قائمة الكشوفات.');
        }

        return redirect()->route('invoices.index');
    }

    public function show($id)
    {
        return $this->invoices->show($id);
    }

    public function showLaboratorie($id)
    {
        return $this->invoices->showLaboratorie($id);
    }
}

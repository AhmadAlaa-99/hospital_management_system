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
            'patient_id' => 'required|exists:patients,id',
            'Service_ids' => 'nullable|array',
            'Service_ids.*' => 'exists:Services,id',
            'Group_ids' => 'nullable|array',
            'Group_ids.*' => 'exists:groups,id',
            'type' => 'required|in:1,2',
            'appointment_id' => 'nullable|exists:appointments,id',
            'return_to' => 'nullable|in:queue,invoices',
        ], [
            'patient_id.required' => 'يرجى اختيار المريض.',
            'type.required' => 'يرجى اختيار نوع الدفع.',
        ]);

        $serviceIds = array_values(array_unique(array_filter($data['Service_ids'] ?? [])));
        $groupIds = array_values(array_unique(array_filter($data['Group_ids'] ?? [])));

        if (empty($serviceIds) && empty($groupIds)) {
            return back()->withErrors(['error' => 'يرجى اختيار خدمة مفردة أو مجموعة خدمات واحدة على الأقل.'])->withInput();
        }

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

        $createdCount = 0;

        foreach ($serviceIds as $serviceId) {
            $billing->createServiceInvoice(array_merge($payload, [
                'Service_id' => $serviceId,
            ]));
            $createdCount++;
        }

        foreach ($groupIds as $groupId) {
            $billing->createGroupInvoice(array_merge($payload, [
                'Group_id' => $groupId,
            ]));
            $createdCount++;
        }

        session()->flash('add', 'تم إنشاء ' . $createdCount . ' فاتورة وستظهر في قائمة الكشوفات.');

        if (($data['return_to'] ?? 'invoices') === 'queue') {
            return redirect()->route('doctor.queue.index');
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

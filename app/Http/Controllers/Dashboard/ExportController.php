<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Services\ReportDataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function insuranceClaims(): StreamedResponse
    {
        $claims = InsuranceClaim::with(['patient', 'insurance'])->latest()->get();

        return $this->csv('insurance-claims', [
            '#', 'المريض', 'شركة التأمين', 'الإجمالي', 'تحمل الشركة', 'تحمل المريض', 'الحالة', 'التاريخ',
        ], $claims->map(function ($claim) {
            return [
                $claim->id,
                optional($claim->patient)->name,
                optional($claim->insurance)->name,
                $claim->total_amount,
                $claim->company_amount,
                $claim->patient_amount,
                InsuranceClaim::$statusLabels[$claim->status] ?? $claim->status,
                optional($claim->claim_date)->format('Y-m-d'),
            ];
        }));
    }

    public function reports(Request $request, ReportDataService $reportData): StreamedResponse
    {
        $data = $reportData->build();
        $rows = collect();

        $rows->push(['=== ملخص الإحصائيات ===']);
        $rows->push(['البند', 'القيمة']);
        $rows->push(['إجمالي المرضى', $data['stats']['total_patients']]);
        $rows->push(['إجمالي الإيرادات', $data['stats']['total_revenue']]);
        $rows->push(['مواعيد مؤكدة', $data['stats']['confirmed_appointments']]);
        $rows->push(['متوسط تقييم الأطباء', $data['stats']['avg_doctor_rating']]);
        $rows->push(['مواعيد مرفوضة (No-Show)', $data['stats']['no_show_count']]);
        $rows->push(['استشارات عن بُعد', $data['stats']['telemedicine_count']]);
        $rows->push(['مواعيد طوارئ/إسعاف', $data['stats']['emergency_appointments']]);
        $rows->push(['مطالبات تأمين معلقة', $data['stats']['pending_claims']]);
        $rows->push([]);

        $rows->push(['=== المرضى والإيرادات الشهرية (6 أشهر) ===']);
        $rows->push(['الشهر', 'مرضى جدد', 'الإيرادات']);
        foreach ($data['months'] as $month) {
            $rows->push([
                $month,
                $data['patientsByMonth'][$month] ?? 0,
                $data['revenueByMonth'][$month] ?? 0,
            ]);
        }
        $rows->push([]);

        $rows->push(['=== أداء الأقسام ===']);
        $rows->push(['القسم', 'الأطباء', 'المواعيد', 'الفواتير', 'الإيرادات']);
        foreach ($data['sectionPerformance'] as $row) {
            $rows->push([
                $row['name'],
                $row['doctors'],
                $row['appointments'],
                $row['invoices'],
                $row['revenue'],
            ]);
        }
        $rows->push([]);

        $rows->push(['=== أكثر التشخيصات ===']);
        $rows->push(['التشخيص', 'العدد']);
        foreach ($data['topDiagnoses'] as $d) {
            $rows->push([$d->diagnosis, $d->total]);
        }
        $rows->push([]);

        $rows->push(['=== متوسط انتظار العيادات (دقيقة) ===']);
        $rows->push(['القسم', 'متوسط الانتظار']);
        foreach ($data['sectionWaitStats'] as $s) {
            $rows->push([$s['name'], $s['avg_wait_minutes']]);
        }

        return $this->csv('reports-full', [''], $rows);
    }

    public function reportsPdf(ReportDataService $reportData)
    {
        $data = $reportData->build();

        $pdf = Pdf::loadView('pdf.reports', $data)->setPaper('a4', 'portrait');

        return $pdf->download('reports-' . date('Y-m-d') . '.pdf');
    }

    public function table(Request $request): StreamedResponse
    {
        $type = $request->get('type', 'patients');

        return match ($type) {
            'appointments' => $this->exportAppointments(),
            'doctors' => $this->exportDoctors(),
            'invoices' => $this->exportInvoices(),
            default => $this->exportPatients(),
        };
    }

    protected function exportPatients(): StreamedResponse
    {
        $rows = Patient::latest()->get()->map(fn ($p) => [$p->id, $p->name, $p->email, $p->phone]);

        return $this->csv('patients', ['#', 'الاسم', 'البريد', 'الهاتف'], $rows);
    }

    protected function exportAppointments(): StreamedResponse
    {
        $rows = Appointment::with(['section', 'doctor'])->latest()->get()->map(fn ($a) => [
            $a->id,
            $a->name,
            optional($a->section)->name,
            optional($a->doctor)->name,
            $a->type,
            $a->preferred_date,
        ]);

        return $this->csv('appointments', ['#', 'المريض', 'القسم', 'الطبيب', 'الحالة', 'التاريخ'], $rows);
    }

    protected function exportDoctors(): StreamedResponse
    {
        $rows = Doctor::with('section')->latest()->get()->map(fn ($d) => [
            $d->id,
            $d->name,
            $d->email,
            $d->phone,
            optional($d->section)->name,
        ]);

        return $this->csv('doctors', ['#', 'الاسم', 'البريد', 'الهاتف', 'القسم'], $rows);
    }

    protected function exportInvoices(): StreamedResponse
    {
        $rows = Invoice::with(['Patient', 'Doctor', 'Section', 'Service', 'Group'])->latest()->get()->map(function ($i) {
            $paymentType = (int) $i->type === 1 ? 'نقدي' : 'اجل';
            $invoiceKind = (int) $i->invoice_type === 1 ? 'خدمة مفردة' : 'مجموعة';

            return [
                $i->id,
                optional($i->Patient)->name,
                optional($i->Doctor)->name,
                optional($i->Section)->name,
                $invoiceKind,
                optional($i->Service)->name ?? optional($i->Group)->name,
                $i->price,
                $i->discount_value,
                $i->tax_rate,
                $i->tax_value,
                $i->total_with_tax,
                $paymentType,
                $i->invoice_date,
            ];
        });

        return $this->csv('invoices', [
            '#', 'المريض', 'الطبيب', 'القسم', 'نوع الفاتورة', 'الخدمة/المجموعة',
            'السعر', 'الخصم', 'نسبة الضريبة', 'قيمة الضريبة', 'الإجمالي', 'نوع الدفع', 'التاريخ',
        ], $rows);
    }

    protected function csv(string $filename, array $headers, $rows): StreamedResponse
    {
        $callback = function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            if (!empty($headers) && $headers !== ['']) {
                fputcsv($file, $headers);
            }
            foreach ($rows as $row) {
                fputcsv($file, is_array($row) ? $row : $row->toArray());
            }
            fclose($file);
        };

        return Response::streamDownload($callback, $filename . '-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\FundAccount;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Section;
use Carbon\Carbon;
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

    public function reports(Request $request): StreamedResponse
    {
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->format('Y-m'));

        $rows = collect();
        foreach ($months as $month) {
            $rows->push([
                $month,
                Patient::whereYear('created_at', substr($month, 0, 4))
                    ->whereMonth('created_at', substr($month, 5, 2))
                    ->count(),
                FundAccount::whereYear('date', substr($month, 0, 4))
                    ->whereMonth('date', substr($month, 5, 2))
                    ->sum('Debit'),
            ]);
        }

        return $this->csv('reports-summary', ['الشهر', 'مرضى جدد', 'الإيرادات'], $rows);
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
        $rows = Invoice::with(['Patient', 'Doctor', 'Section', 'Service'])->latest()->get()->map(fn ($i) => [
            $i->id,
            optional($i->Patient)->name,
            optional($i->Doctor)->name,
            optional($i->Section)->name,
            optional($i->Service)->name,
            $i->total_with_tax,
            $i->invoice_date,
        ]);

        return $this->csv('invoices', ['#', 'المريض', 'الطبيب', 'القسم', 'الخدمة', 'الإجمالي', 'التاريخ'], $rows);
    }

    protected function csv(string $filename, array $headers, $rows): StreamedResponse
    {
        $callback = function () use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, $headers);
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

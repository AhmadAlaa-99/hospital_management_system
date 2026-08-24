<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Diagnostic;
use App\Models\DoctorRating;
use App\Models\FundAccount;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportDataService
{
    public function build(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i)->format('Y-m'));

        $patientsByMonth = $months->mapWithKeys(function ($month) {
            $count = Patient::whereYear('created_at', substr($month, 0, 4))
                ->whereMonth('created_at', substr($month, 5, 2))
                ->count();

            return [$month => $count];
        });

        $revenueByMonth = $months->mapWithKeys(function ($month) {
            $total = FundAccount::whereYear('date', substr($month, 0, 4))
                ->whereMonth('date', substr($month, 5, 2))
                ->sum('Debit');

            return [$month => (float) $total];
        });

        $sectionPerformance = Section::withCount('doctors')->get()->map(function ($section) {
            $doctorIds = $section->doctors()->pluck('id');
            $invoiceIds = Invoice::where('section_id', $section->id)->pluck('id');

            return [
                'name' => $section->name,
                'doctors' => $section->doctors_count,
                'appointments' => Appointment::whereIn('doctor_id', $doctorIds)->where('type', 'مؤكد')->count(),
                'invoices' => $invoiceIds->count(),
                'revenue' => (float) FundAccount::whereIn('invoice_id', $invoiceIds)->sum('Debit'),
            ];
        });

        $stats = [
            'total_patients' => Patient::count(),
            'total_revenue' => FundAccount::sum('Debit'),
            'confirmed_appointments' => Appointment::where('type', 'مؤكد')->count(),
            'pending_claims' => InsuranceClaim::where('status', 'pending')->count(),
            'avg_doctor_rating' => round(DoctorRating::avg('rating') ?: 0, 1),
            'no_show_count' => Appointment::where('type', 'مرفوض')->count(),
            'telemedicine_count' => Appointment::where('consultation_type', 'telemedicine')->count(),
            'emergency_appointments' => Appointment::where('is_emergency', true)->count(),
        ];

        $topDiagnoses = Diagnostic::select('diagnosis', DB::raw('count(*) as total'))
            ->whereNotNull('diagnosis')
            ->groupBy('diagnosis')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $sectionWaitStats = Section::with('translations')->get()->map(function ($section) {
            $doctorIds = $section->doctors()->pluck('id');
            $avgWait = QueueTicket::whereIn('doctor_id', $doctorIds)
                ->whereNotNull('called_at')
                ->whereNotNull('created_at')
                ->get()
                ->avg(function ($ticket) {
                    return $ticket->called_at && $ticket->created_at
                        ? $ticket->created_at->diffInMinutes($ticket->called_at)
                        : null;
                });

            return [
                'name' => $section->name,
                'avg_wait_minutes' => round($avgWait ?: 0, 1),
            ];
        });

        return compact(
            'months',
            'patientsByMonth',
            'revenueByMonth',
            'sectionPerformance',
            'stats',
            'topDiagnoses',
            'sectionWaitStats'
        );
    }
}

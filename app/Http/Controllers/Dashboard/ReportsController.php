<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorRating;
use App\Models\FundAccount;
use App\Models\InsuranceClaim;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

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
        ];

        return view('Dashboard.Reports.index', compact(
            'patientsByMonth', 'revenueByMonth', 'sectionPerformance', 'stats', 'months'
        ));
    }
}

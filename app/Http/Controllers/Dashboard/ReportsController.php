<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ReportDataService;

class ReportsController extends Controller
{
    public function index(ReportDataService $reportData)
    {
        return view('Dashboard.Reports.index', $reportData->build());
    }
}

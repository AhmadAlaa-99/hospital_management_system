<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\ExternalRecord;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExternalRecordController extends Controller
{
    public function index()
    {
        $records = ExternalRecord::where('patient_id', Auth::guard('patient')->id())
            ->latest()
            ->paginate(20);

        return view('Dashboard.dashboard_patient.external_records.index', compact('records'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'type' => 'required|in:lab,ray,report,prescription,other',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        $path = $request->file('file')->store('external-records/' . Auth::guard('patient')->id(), 'public');

        $record = ExternalRecord::create([
            'patient_id' => Auth::guard('patient')->id(),
            'title' => $data['title'],
            'type' => $data['type'],
            'file_path' => $path,
            'notes' => $data['notes'] ?? null,
        ]);

        AuditLogService::log('external_record_uploaded', $record);

        session()->flash('add');
        return back();
    }

    public function download(ExternalRecord $externalRecord)
    {
        if ($externalRecord->patient_id !== Auth::guard('patient')->id()) {
            abort(403);
        }

        return Storage::disk('public')->download($externalRecord->file_path, $externalRecord->title);
    }

    public function destroy(ExternalRecord $externalRecord)
    {
        if ($externalRecord->patient_id !== Auth::guard('patient')->id()) {
            abort(403);
        }

        Storage::disk('public')->delete($externalRecord->file_path);
        $externalRecord->delete();

        session()->flash('delete');
        return back();
    }
}

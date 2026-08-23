<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\ExternalRecord;
use App\Helpers\FriendlyError;
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
        ], [
            'title.required' => 'يرجى إدخال عنوان الملف.',
            'file.required' => 'يرجى اختيار ملف للرفع.',
            'file.mimes' => 'الملفات المسموحة: PDF أو صورة (jpg, png).',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 5 ميغابايت.',
        ]);

        $patientId = Auth::guard('patient')->id();

        try {
            $disk = Storage::disk('public');
            $directory = 'external-records/' . $patientId;

            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }

            $path = $request->file('file')->store($directory, 'public');

            $record = ExternalRecord::create([
                'patient_id' => $patientId,
                'title' => $data['title'],
                'type' => $data['type'],
                'file_path' => $path,
                'notes' => $data['notes'] ?? null,
            ]);

            try {
                AuditLogService::log('external_record_uploaded', $record);
            } catch (\Throwable $e) {
                report($e);
            }

            session()->flash('add');
            return back();
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->withErrors([
                'error' => FriendlyError::message($e->getMessage()),
            ]);
        }
    }

    public function download(ExternalRecord $externalRecord)
    {
        if ($externalRecord->patient_id !== Auth::guard('patient')->id()) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($externalRecord->file_path)) {
            return back()->withErrors(['error' => 'الملف غير موجود على الخادم.']);
        }

        return Storage::disk('public')->download($externalRecord->file_path, $externalRecord->title);
    }

    public function destroy(ExternalRecord $externalRecord)
    {
        if ($externalRecord->patient_id !== Auth::guard('patient')->id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($externalRecord->file_path)) {
            Storage::disk('public')->delete($externalRecord->file_path);
        }

        $externalRecord->delete();

        session()->flash('delete');
        return back();
    }
}

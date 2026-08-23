<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\ExternalRecord;
use App\Helpers\FriendlyError;
use App\Services\AuditLogService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExternalRecordController extends Controller
{
    /** @var string Disk for patient uploads (storage/app/...) */
    protected string $uploadDisk = 'local';

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
            $directory = 'external-records/' . $patientId;
            $this->ensureUploadDirectory($directory);

            $path = $request->file('file')->store($directory, $this->uploadDisk);

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

        $resolved = $this->resolveStoredFile($externalRecord->file_path);

        if (!$resolved) {
            return back()->withErrors(['error' => 'الملف غير موجود على الخادم.']);
        }

        return $resolved['disk']->download($resolved['path'], $this->downloadFilename($externalRecord));
    }

    public function destroy(ExternalRecord $externalRecord)
    {
        if ($externalRecord->patient_id !== Auth::guard('patient')->id()) {
            abort(403);
        }

        $resolved = $this->resolveStoredFile($externalRecord->file_path);

        if ($resolved) {
            $resolved['disk']->delete($resolved['path']);
        }

        $externalRecord->delete();

        session()->flash('delete');
        return back();
    }

    protected function ensureUploadDirectory(string $directory): void
    {
        $disk = Storage::disk($this->uploadDisk);

        if (!$disk->exists('external-records')) {
            $disk->makeDirectory('external-records', 0755, true);
        }

        if (!$disk->exists($directory)) {
            $disk->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * @return array{disk: Filesystem, path: string}|null
     */
    protected function resolveStoredFile(string $filePath): ?array
    {
        foreach (['local', 'public'] as $diskName) {
            $disk = Storage::disk($diskName);
            if ($disk->exists($filePath)) {
                return ['disk' => $disk, 'path' => $filePath];
            }
        }

        return null;
    }

    protected function downloadFilename(ExternalRecord $record): string
    {
        $extension = pathinfo($record->file_path, PATHINFO_EXTENSION);
        $base = preg_replace('/[^\p{L}\p{N}\-_]+/u', '-', $record->title) ?: 'medical-file';

        return $extension ? ($base . '.' . $extension) : $base;
    }
}

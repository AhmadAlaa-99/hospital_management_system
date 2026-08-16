<?php

namespace App\Http\Controllers\Dashboard_Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorRating;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class DoctorRatingController extends Controller
{
    public function create(Appointment $appointment)
    {
        $patient = auth()->user();
        if ($appointment->email !== $patient->email || $appointment->type !== 'منتهي') {
            abort(403);
        }
        if (DoctorRating::where('appointment_id', $appointment->id)->exists()) {
            return redirect()->route('patient.appointments')->with('info', 'تم تقييم هذا الموعد مسبقاً');
        }

        return view('Dashboard.dashboard_patient.ratings.create', compact('appointment'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $patient = auth()->user();
        if ($appointment->email !== $patient->email || $appointment->type !== 'منتهي') {
            abort(403);
        }

        if (DoctorRating::where('appointment_id', $appointment->id)->exists()) {
            return redirect()->route('patient.appointments')->with('info', 'تم تقييم هذا الموعد مسبقاً');
        }

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'share_on_homepage' => 'sometimes|boolean',
        ]);

        $shareOnHomepage = $request->boolean('share_on_homepage');

        if ($shareOnHomepage && trim((string) ($data['comment'] ?? '')) === '') {
            return back()
                ->withInput()
                ->withErrors(['comment' => 'يجب كتابة نص المراجعة عند طلب نشرها في الصفحة الرئيسية.']);
        }

        if ($shareOnHomepage && mb_strlen(trim((string) $data['comment'])) < 15) {
            return back()
                ->withInput()
                ->withErrors(['comment' => 'نص المراجعة قصير جداً — اكتب 15 حرفاً على الأقل.']);
        }

        DoctorRating::create([
            'doctor_id' => $appointment->doctor_id,
            'patient_id' => $patient->id,
            'appointment_id' => $appointment->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
            'share_on_homepage' => $shareOnHomepage,
            'homepage_status' => $shareOnHomepage
                ? DoctorRating::HOMEPAGE_PENDING
                : DoctorRating::HOMEPAGE_NONE,
        ]);

        if ($shareOnHomepage) {
            $doctorName = optional($appointment->doctor)->name ?: 'طبيب';
            NotificationService::notifyAdmin(
                'مراجعة مريض جديدة بانتظار الموافقة: ' . $patient->name . ' — ' . $doctorName,
                route('patient-testimonials.index', ['status' => 'pending'])
            );
        }

        session()->flash('add');

        $message = $shareOnHomepage
            ? 'تم إرسال تقييمك. مراجعتك بانتظار موافقة الإدارة للظهور في الصفحة الرئيسية.'
            : 'تم حفظ تقييمك بنجاح.';

        return redirect()->route('patient.appointments')->with('success', $message);
    }
}

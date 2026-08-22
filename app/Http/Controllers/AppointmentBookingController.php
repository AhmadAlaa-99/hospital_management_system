<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AppointmentScheduleService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentBookingController extends Controller
{
    public function doctors(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
        ]);

        $doctors = Doctor::with('translations')
            ->where('section_id', $request->section_id)
            ->where('status', 1)
            ->get()
            ->map(function (Doctor $doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->name ?: ('#' . $doctor->id),
                ];
            })
            ->values();

        return response()->json(['doctors' => $doctors]);
    }

    public function slots(Request $request, AppointmentScheduleService $scheduleService)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $doctor = Doctor::where('id', $request->doctor_id)->where('status', 1)->first();
        if (!$doctor) {
            return response()->json(['slots' => [], 'message' => 'الطبيب غير متاح حالياً']);
        }

        $result = $scheduleService->resolveAvailableSlots((int) $request->doctor_id, $request->date);

        return response()->json([
            'slots' => $result['slots'],
            'message' => $result['message'],
        ]);
    }

    public function store(Request $request, AppointmentScheduleService $scheduleService)
    {
        $patient = $request->user('patient');
        if (!$patient) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'auth_required' => true,
                    'message' => 'يلزم تسجيل الدخول كمريض لحجز موعد',
                ], 401);
            }

            return redirect()->route('home')->with('error', 'يلزم تسجيل الدخول كمريض لحجز موعد');
        }

        $data = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'doctor_id' => 'required|exists:doctors,id',
            'notes' => 'nullable|string|max:500',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'required|date_format:H:i',
            'consultation_type' => 'nullable|in:in_person,telemedicine',
            'meeting_url' => 'nullable|url|max:500',
        ], [
            'section_id.required' => 'يرجى اختيار القسم',
            'doctor_id.required' => 'يرجى اختيار الدكتور',
            'preferred_date.required' => 'يرجى اختيار التاريخ المفضل',
            'preferred_date.after_or_equal' => 'لا يمكن اختيار تاريخ في الماضي',
            'preferred_time.required' => 'يرجى اختيار الوقت المفضل',
            'preferred_time.date_format' => 'صيغة الوقت غير صحيحة',
        ]);

        $data['name'] = (string) ($patient->name ?: 'مريض');
        $data['email'] = $patient->email;
        $data['phone'] = (string) ($patient->Phone ?? '');

        $doctorBelongsToSection = Doctor::where('id', $data['doctor_id'])
            ->where('section_id', $data['section_id'])
            ->where('status', 1)
            ->exists();

        if (!$doctorBelongsToSection) {
            throw ValidationException::withMessages([
                'doctor_id' => 'الدكتور غير تابع للقسم المختار',
            ]);
        }

        $preferred = Carbon::parse($data['preferred_date'] . ' ' . $data['preferred_time']);
        $error = $scheduleService->validateSlot((int) $data['doctor_id'], $preferred);
        if ($error) {
            throw ValidationException::withMessages(['preferred_time' => $error]);
        }
        if ($scheduleService->countPendingAtSlot((int) $data['doctor_id'], $preferred) >= 3) {
            throw ValidationException::withMessages([
                'preferred_time' => 'هذا الوقت مزدحم بطلبات قيد المراجعة — يرجى اختيار وقت آخر',
            ]);
        }

        if (strlen($data['phone']) < 8) {
            throw ValidationException::withMessages([
                'phone' => 'يرجى تحديث رقم الهاتف في ملفك الشخصي قبل حجز الموعد',
            ]);
        }

        Appointment::create([
            'doctor_id' => $data['doctor_id'],
            'section_id' => $data['section_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'notes' => $data['notes'] ?? null,
            'preferred_date' => $data['preferred_date'] ?? null,
            'preferred_time' => $data['preferred_time'] ?? null,
            'type' => 'غير مؤكد',
            'consultation_type' => $data['consultation_type'] ?? 'in_person',
            'meeting_url' => $data['meeting_url'] ?? null,
        ]);

        NotificationService::notifyDoctor(
            (int) $data['doctor_id'],
            'حجز موعد جديد من المريض: ' . $data['name']
        );

        NotificationService::notifyAdmin(
            'طلب موعد جديد: ' . $data['name'],
            route('appointments.index')
        );

        NotificationService::notifyPatient(
            (int) $patient->id,
            'تم استلام طلب موعدك وهو قيد المراجعة.'
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم ارسال تفاصيل الحجز الى المستشفى وسيتم ارسال معلومات الموعد عبر الهاتف والبريد الالكتروني',
            ]);
        }

        return back()->with('appointment_success', true);
    }
}

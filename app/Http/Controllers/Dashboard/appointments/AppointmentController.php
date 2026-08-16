<?php

namespace App\Http\Controllers\Dashboard\appointments;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use App\Services\AppointmentScheduleService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['doctor', 'section'])
            ->where('type', 'غير مؤكد')
            ->latest()
            ->get();

        return view('Dashboard.appointments.index', compact('appointments'));
    }

    public function index2()
    {
        $appointments = Appointment::with(['doctor', 'section'])
            ->where('type', 'مؤكد')
            ->latest()
            ->get();

        return view('Dashboard.appointments.index2', compact('appointments'));
    }

    public function index3()
    {
        $appointments = Appointment::with(['doctor', 'section'])
            ->whereIn('type', ['منتهي', 'مرفوض'])
            ->latest()
            ->get();

        return view('Dashboard.appointments.index3', compact('appointments'));
    }

    public function approval(Request $request, $id, AppointmentScheduleService $scheduleService)
    {
        $request->validate([
            'appointment' => 'required|date',
        ]);

        $appointment = Appointment::findOrFail($id);
        $datetime = Carbon::parse($request->appointment);

        $error = DB::transaction(function () use ($scheduleService, $appointment, $datetime, $request, $id) {
            $locked = Appointment::lockForUpdate()->findOrFail($id);

            $slotError = $scheduleService->validateSlot(
                (int) $locked->doctor_id,
                $datetime,
                $locked->id
            );

            if ($slotError) {
                return $slotError;
            }

            $locked->update([
                'type' => 'مؤكد',
                'appointment' => $request->appointment,
            ]);

            return null;
        });

        if ($error) {
            return back()->withErrors(['appointment' => $error]);
        }

        $appointment->refresh();

        NotificationService::notifyDoctor(
            (int) $appointment->doctor_id,
            'تم تأكيد موعد المريض: ' . $appointment->name . ' بتاريخ ' . $appointment->appointment
        );

        NotificationService::notifyPatientByEmail(
            $appointment->email,
            'تم تأكيد موعدك بتاريخ ' . $appointment->appointment
        );

        try {
            Mail::to($appointment->email)->send(
                new AppointmentConfirmation($appointment->name, $appointment->appointment)
            );
        } catch (\Throwable $e) {
            // لا نوقف التأكيد إذا فشل الإيميل
        }

        $account_sid = env('TWILIO_SID');
        $auth_token = env('TWILIO_TOKEN');
        $twilio_number = env('TWILIO_FROM');

        if ($account_sid && $auth_token && $twilio_number) {
            try {
                $receiverNumber = $appointment->phone;
                $message = 'عزيزي المريض ' . $appointment->name . ' تم حجز موعدك بتاريخ ' . $appointment->appointment;
                $client = new Client($account_sid, $auth_token);
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number,
                    'body' => $message,
                ]);
            } catch (\Throwable $e) {
                // لا نوقف التأكيد إذا فشل SMS
            }
        }

        session()->flash('add');
        return back();
    }

    public function refuse($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['type' => 'مرفوض']);

        NotificationService::notifyDoctor(
            (int) $appointment->doctor_id,
            'تم رفض موعد المريض: ' . $appointment->name
        );

        NotificationService::notifyPatientByEmail(
            $appointment->email,
            'تم رفض طلب الموعد الخاص بك. يرجى التواصل مع المستشفى.'
        );

        session()->flash('delete');
        return back();
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['type' => 'منتهي']);

        NotificationService::notifyPatientByEmail(
            $appointment->email,
            'تم انهاء موعدك بتاريخ ' . ($appointment->appointment ?: now()->toDateString())
        );

        session()->flash('delete');
        return back();
    }
}

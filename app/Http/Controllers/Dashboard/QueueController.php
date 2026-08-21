<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Models\Section;
use App\Services\QueueService;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::all();
        $doctors = Doctor::where('status', 1)->get();

        $sectionId = $request->get('section_id', optional($sections->first())->id);
        $doctorId = $request->get('doctor_id');

        $tickets = QueueTicket::with(['doctor', 'section', 'appointment'])
            ->today()
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->orderByRaw("FIELD(status, 'serving', 'called', 'waiting', 'completed', 'no_show', 'cancelled')")
            ->orderBy('daily_sequence')
            ->get();

        $appointmentsToday = Appointment::with(['doctor', 'section'])
            ->where('type', 'مؤكد')
            ->whereDate('appointment', today())
            ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->get()
            ->filter(function ($apt) {
                return !QueueTicket::today()->where('appointment_id', $apt->id)
                    ->whereNotIn('status', ['cancelled'])->exists();
            });

        $patients = Patient::orderBy('id')->get();

        return view('Dashboard.Queue.index', compact(
            'sections', 'doctors', 'tickets', 'appointmentsToday', 'sectionId', 'doctorId',
            'patients'
        ));
    }

    public function store(Request $request, QueueService $queue)
    {
        $flow = $request->input('flow', 'scheduled');

        try {
            if ($flow === 'scheduled') {
                $request->validate([
                    'appointment_id' => 'required|exists:appointments,id',
                    'section_id' => 'required|exists:sections,id',
                ]);

                $appointment = Appointment::findOrFail($request->appointment_id);
                $result = $queue->checkInAppointment($appointment);
                $message = $result['created']
                    ? 'تم إصدار رقم ' . $result['ticket']->ticket_number . ' للموعد المجدول'
                    : 'المريض مسجّل مسبقاً — رقم ' . $result['ticket']->ticket_number;
                session()->flash('add', $message);

                return back();
            }

            if ($flow === 'existing') {
                $data = $request->validate([
                    'section_id' => 'required|exists:sections,id',
                    'doctor_id' => 'required|exists:doctors,id',
                    'patient_id' => 'required|exists:patients,id',
                    'priority' => 'nullable|in:normal,urgent,elderly',
                ]);

                $ticket = $queue->issueForExistingPatient($data);
                session()->flash('add', 'تم ربط الموعد وإصدار رقم ' . $ticket->ticket_number);

                return back();
            }

            if ($flow === 'new') {
                $data = $request->validate([
                    'section_id' => 'required|exists:sections,id',
                    'doctor_id' => 'required|exists:doctors,id',
                    'name' => 'required|string|min:2|max:100',
                    'email' => 'required|email|unique:patients,email',
                    'phone' => 'required|string|min:8|max:20|unique:patients,Phone',
                    'password' => 'required|string|min:6|confirmed',
                    'priority' => 'nullable|in:normal,urgent,elderly',
                ], [
                    'name.required' => 'يرجى إدخال الاسم الكامل.',
                    'name.min' => 'الاسم يجب أن يكون حرفين على الأقل.',
                    'email.required' => 'يرجى إدخال البريد الإلكتروني.',
                    'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
                    'email.unique' => 'هذا البريد مسجّل مسبقاً — استخدم تبويب «مريض مسجّل».',
                    'phone.required' => 'يرجى إدخال رقم الهاتف.',
                    'phone.min' => 'رقم الهاتف قصير جداً.',
                    'phone.unique' => 'رقم الهاتف مسجّل مسبقاً — استخدم تبويب «مريض مسجّل».',
                    'password.required' => 'يرجى إدخال كلمة المرور.',
                    'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
                    'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
                ]);

                $ticket = $queue->registerPatientAndIssue($data);
                session()->flash('add', 'تم إنشاء حساب المريض والموعد ورقم ' . $ticket->ticket_number);

                return back();
            }

            throw new \RuntimeException('نوع العملية غير صالح');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function checkIn(Appointment $appointment, QueueService $queue)
    {
        try {
            $result = $queue->checkInAppointment($appointment);
            $message = $result['created']
                ? 'تم تسجيل الحضور — رقم ' . $result['ticket']->ticket_number
                : 'المريض مسجّل مسبقاً — رقم ' . $result['ticket']->ticket_number;
            session()->flash('add', $message);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back();
    }

    public function cancel(QueueTicket $ticket, QueueService $queue)
    {
        $queue->cancel($ticket);
        session()->flash('delete');
        return back();
    }
}

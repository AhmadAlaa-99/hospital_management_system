<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
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

        return view('Dashboard.Queue.index', compact(
            'sections', 'doctors', 'tickets', 'appointmentsToday', 'sectionId', 'doctorId'
        ));
    }

    public function store(Request $request, QueueService $queue)
    {
        if ($request->filled('appointment_id')) {
            $appointment = Appointment::findOrFail($request->appointment_id);

            try {
                $result = $queue->checkInAppointment($appointment);
                $message = $result['created']
                    ? 'تم إصدار رقم ' . $result['ticket']->ticket_number . ' للموعد المجدول'
                    : 'المريض مسجّل مسبقاً — رقم ' . $result['ticket']->ticket_number;
                session()->flash('add', $message);
            } catch (\RuntimeException $e) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }

            return back();
        }

        $data = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|min:2|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'priority' => 'nullable|in:normal,urgent,elderly',
        ]);

        try {
            $ticket = $queue->issueWalkInTicket($data);
            session()->flash('add', 'تم إنشاء موعد للمريض وإصدار رقم ' . $ticket->ticket_number);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back();
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

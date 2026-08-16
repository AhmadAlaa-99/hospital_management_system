<?php

namespace App\Http\Controllers\Dashboard_Doctor;

use App\Http\Controllers\Controller;
use App\Models\QueueTicket;
use App\Services\QueueService;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(QueueService $queue)
    {
        $doctor = auth('doctor')->user();

        $tickets = QueueTicket::with(['section', 'appointment'])
            ->today()
            ->where('doctor_id', $doctor->id)
            ->orderByRaw("FIELD(status, 'serving', 'called', 'waiting', 'completed', 'no_show', 'cancelled')")
            ->orderBy('daily_sequence')
            ->get();

        $display = $queue->getDisplayData($doctor->section_id, $doctor->id);

        return view('Dashboard.doctor.queue.index', compact('tickets', 'display', 'doctor'));
    }

    public function callNext(QueueService $queue)
    {
        $doctor = auth('doctor')->user();
        $ticket = $queue->callNext($doctor->section_id, $doctor->id);

        if (!$ticket) {
            return back()->withErrors(['error' => 'لا يوجد مرضى في الانتظار']);
        }

        session()->flash('add', 'تم نداء ' . $ticket->ticket_number);
        return back();
    }

    public function recall(QueueTicket $ticket, QueueService $queue)
    {
        $this->authorizeDoctorTicket($ticket);
        try {
            $queue->recall($ticket);
            session()->flash('add', 'تم إعادة نداء ' . $ticket->ticket_number);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back();
    }

    public function serving(QueueTicket $ticket, QueueService $queue)
    {
        $this->authorizeDoctorTicket($ticket);
        try {
            $queue->markServing($ticket);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back();
    }

    public function complete(QueueTicket $ticket, QueueService $queue)
    {
        $this->authorizeDoctorTicket($ticket);
        try {
            $queue->complete($ticket);
            session()->flash('edit');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back();
    }

    public function noShow(QueueTicket $ticket, QueueService $queue)
    {
        $this->authorizeDoctorTicket($ticket);
        try {
            $queue->noShow($ticket);
            session()->flash('delete');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
        return back();
    }

    protected function authorizeDoctorTicket(QueueTicket $ticket): void
    {
        if ((int) $ticket->doctor_id !== (int) auth('doctor')->id()) {
            abort(403);
        }
    }
}

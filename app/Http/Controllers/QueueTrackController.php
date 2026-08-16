<?php

namespace App\Http\Controllers;

use App\Services\QueueService;
use Illuminate\Http\Request;

class QueueTrackController extends Controller
{
    public function show()
    {
        return view('queue.track', [
            'searched' => false,
            'ticket' => null,
        ]);
    }

    public function lookup(Request $request, QueueService $queue)
    {
        $request->validate(['ticket_number' => 'required|string|max:20']);

        $ticket = $queue->trackTicket(strtoupper(trim($request->ticket_number)));

        if ($request->expectsJson()) {
            return response()->json([
                'found' => (bool) $ticket,
                'ticket' => $ticket,
                'message' => $ticket ? 'تم العثور على التذكرة' : 'لم يتم العثور على هذا الرقم اليوم',
            ]);
        }

        return view('queue.track', [
            'searched' => true,
            'ticket' => $ticket,
            'ticket_number' => strtoupper(trim($request->ticket_number)),
        ]);
    }
}
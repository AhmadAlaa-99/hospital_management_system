<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Section;
use App\Services\QueueService;
use Illuminate\Http\Request;

class QueueDisplayController extends Controller
{
    public function index(QueueService $queue)
    {
        $section = Section::orderBy('id')->first();
        if (!$section) {
            abort(404, 'لا توجد أقسام');
        }

        return redirect()->route('queue.display.section', $section);
    }

    public function section(Section $section, QueueService $queue)
    {
        $data = $queue->getDisplayData($section->id);

        return view('queue.display', $this->displayViewData($queue, [
            'title' => 'شاشة الانتظار — ' . $section->name,
            'scope' => 'section',
            'scopeId' => $section->id,
            'sectionId' => $section->id,
            'data' => $data,
        ]));
    }

    public function doctor(Doctor $doctor, QueueService $queue)
    {
        $data = $queue->getDisplayData($doctor->section_id, $doctor->id);

        return view('queue.display', $this->displayViewData($queue, [
            'title' => 'شاشة الانتظار — د. ' . $doctor->name,
            'scope' => 'doctor',
            'scopeId' => $doctor->id,
            'sectionId' => $doctor->section_id,
            'data' => $data,
        ]));
    }

    protected function displayViewData(QueueService $queue, array $base): array
    {
        $sections = Section::orderBy('id')->get()->map(function (Section $section) use ($queue) {
            return [
                'id' => $section->id,
                'name' => $section->name,
                'code' => $queue->sectionTicketCode($section->id),
                'label' => $queue->sectionTicketLabel($section->id),
                'url' => route('queue.display.section', $section),
            ];
        });

        return array_merge($base, [
            'sections' => $sections,
            'queueService' => $queue,
        ]);
    }

    public function data(Request $request, QueueService $queue)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $data = $queue->getDisplayData(
            (int) $request->section_id,
            $request->doctor_id ? (int) $request->doctor_id : null
        );

        return response()->json($queue->serializeDisplayData($data));
    }
}

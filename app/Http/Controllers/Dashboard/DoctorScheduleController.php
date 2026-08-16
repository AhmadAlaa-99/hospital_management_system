<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Section;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index(Request $request)
    {
        $doctors = Doctor::with('section')->where('status', 1)->orderBy('id')->get();
        $sections = Section::with('translations')->orderBy('id')->get();

        $filters = [
            'doctor_id' => $request->input('doctor_id'),
            'section_id' => $request->input('section_id'),
            'day_of_week' => $request->input('day_of_week'),
            'q' => trim((string) $request->input('q')),
        ];

        $query = DoctorSchedule::with(['doctor.section']);

        if ($filters['doctor_id']) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if ($filters['section_id']) {
            $query->whereHas('doctor', function ($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }

        if ($filters['day_of_week'] !== null && $filters['day_of_week'] !== '') {
            $query->where('day_of_week', (int) $filters['day_of_week']);
        }

        if ($filters['q'] !== '') {
            $search = $filters['q'];
            $query->whereHas('doctor', function ($q) use ($search) {
                $q->whereHas('translations', function ($t) use ($search) {
                    $t->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $scheduleRows = $query
            ->orderBy('doctor_id')
            ->orderBy('day_of_week')
            ->get();

        $stats = [
            'total' => DoctorSchedule::count(),
            'filtered' => $scheduleRows->count(),
            'doctors' => $scheduleRows->pluck('doctor_id')->unique()->count(),
        ];

        return view('Dashboard.DoctorSchedules.index', compact(
            'doctors',
            'sections',
            'scheduleRows',
            'filters',
            'stats'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'slot_duration' => 'required|integer|min:15|max:120',
        ], [
            'end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية.',
        ]);

        DoctorSchedule::updateOrCreate(
            ['doctor_id' => $data['doctor_id'], 'day_of_week' => $data['day_of_week']],
            $data
        );

        session()->flash('add');
        return back();
    }

    public function destroy(DoctorSchedule $schedule)
    {
        $schedule->delete();
        session()->flash('delete');
        return back();
    }
}

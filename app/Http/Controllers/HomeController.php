<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Blog;
use App\Models\Doctor;
use App\Models\DoctorRating;
use App\Models\Patient;
use App\Models\Section;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $sections = Section::with(['translations'])->withCount('doctors')->get();
        $doctors = Doctor::with(['section.translations', 'translations', 'image'])
            ->where('status', 1)
            ->latest()
            ->take(8)
            ->get();
        $appointmentDoctors = Doctor::with('translations')
            ->where('status', 1)
            ->get()
            ->groupBy('section_id')
            ->map(function ($items) {
                return $items->map(function (Doctor $doctor) {
                    return [
                        'id' => $doctor->id,
                        'name' => $doctor->name ?: ('#' . $doctor->id),
                    ];
                })->values();
            });
        $stats = [
            'patients' => Patient::count(),
            'doctors' => Doctor::count(),
            'sections' => Section::count(),
            'appointments' => Appointment::where('type', 'مؤكد')->count(),
        ];
        $blogs = Blog::where('is_published', true)
            ->withCount('comments')
            ->latest('published_at')
            ->take(2)
            ->get();
        $siteSetting = SiteSetting::current();
        $patientId = auth('patient')->id();
        $likedIds = $patientId
            ? \App\Models\BlogLike::where('patient_id', $patientId)->pluck('blog_id')->all()
            : [];
        $testimonials = DoctorRating::publishedOnHomepage()
            ->with(['patient', 'doctor.section'])
            ->latest()
            ->take(4)
            ->get();

        return view('welcome', compact('sections', 'doctors', 'appointmentDoctors', 'stats', 'blogs', 'siteSetting', 'likedIds', 'testimonials'));
    }
}

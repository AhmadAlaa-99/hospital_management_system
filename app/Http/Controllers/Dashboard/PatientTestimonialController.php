<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DoctorRating;
use Illuminate\Http\Request;

class PatientTestimonialController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = DoctorRating::with(['patient', 'doctor.section'])
            ->where('share_on_homepage', true)
            ->latest();

        if ($status !== 'all') {
            $query->where('homepage_status', $status);
        }

        $testimonials = $query->paginate(20)->appends(['status' => $status]);

        $counts = [
            'pending' => DoctorRating::where('share_on_homepage', true)
                ->where('homepage_status', DoctorRating::HOMEPAGE_PENDING)->count(),
            'approved' => DoctorRating::where('homepage_status', DoctorRating::HOMEPAGE_APPROVED)->count(),
            'rejected' => DoctorRating::where('share_on_homepage', true)
                ->where('homepage_status', DoctorRating::HOMEPAGE_REJECTED)->count(),
        ];

        return view('Dashboard.PatientTestimonials.index', compact('testimonials', 'status', 'counts'));
    }

    public function approve(DoctorRating $rating)
    {
        if (!$rating->share_on_homepage || blank($rating->comment)) {
            return back()->withErrors(['rating' => 'لا يمكن نشر مراجعة بدون نص.']);
        }

        $rating->update(['homepage_status' => DoctorRating::HOMEPAGE_APPROVED]);
        session()->flash('edit');

        return back();
    }

    public function reject(DoctorRating $rating)
    {
        $rating->update(['homepage_status' => DoctorRating::HOMEPAGE_REJECTED]);
        session()->flash('delete');

        return back();
    }

    public function unpublish(DoctorRating $rating)
    {
        $rating->update(['homepage_status' => DoctorRating::HOMEPAGE_REJECTED]);
        session()->flash('delete');

        return back();
    }
}

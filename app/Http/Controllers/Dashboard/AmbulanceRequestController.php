<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\AmbulanceRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AmbulanceRequestController extends Controller
{
    public function index()
    {
        $requests = AmbulanceRequest::with('ambulance')->latest()->paginate(20);
        $availableAmbulances = Ambulance::availableForDispatch()->get();

        return view('Dashboard.AmbulanceRequests.index', compact('requests', 'availableAmbulances'));
    }

    public function assignAmbulance(Request $request, AmbulanceRequest $ambulanceRequest)
    {
        $request->validate(['ambulance_id' => 'required|exists:ambulances,id']);

        $isAvailable = Ambulance::availableForDispatch()
            ->where('id', $request->ambulance_id)
            ->exists();

        if (!$isAvailable) {
            return back()->withErrors([
                'error' => 'السيارة المختارة غير متاحة — قد تكون مشغولة بطلب إسعاف آخر غير مكتمل.',
            ]);
        }

        if ($ambulanceRequest->status !== 'pending') {
            return back()->withErrors(['error' => 'لا يمكن إسناد سيارة لهذا الطلب في حالته الحالية.']);
        }

        $ambulanceRequest->update([
            'ambulance_id' => $request->ambulance_id,
            'status' => 'dispatched',
        ]);

        Ambulance::where('id', $request->ambulance_id)->update(['is_available' => 2]);

        NotificationService::notifyAdmin('تم إرسال إسعاف للمريض: ' . $ambulanceRequest->patient_name, route('ambulance-requests.index'));

        session()->flash('edit');
        return back();
    }

    public function complete(AmbulanceRequest $ambulanceRequest)
    {
        if ($ambulanceRequest->ambulance_id) {
            Ambulance::where('id', $ambulanceRequest->ambulance_id)->update(['is_available' => 1]);
        }
        $ambulanceRequest->update(['status' => 'completed']);
        session()->flash('edit');
        return back();
    }

    public function cancel(AmbulanceRequest $ambulanceRequest)
    {
        if ($ambulanceRequest->ambulance_id && $ambulanceRequest->status === 'dispatched') {
            Ambulance::where('id', $ambulanceRequest->ambulance_id)->update(['is_available' => 1]);
        }
        $ambulanceRequest->update(['status' => 'cancelled']);
        session()->flash('delete');
        return back();
    }
}

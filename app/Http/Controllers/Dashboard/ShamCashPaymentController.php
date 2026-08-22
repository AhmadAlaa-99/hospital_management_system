<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ShamCashPayment;
use App\Services\ShamCashPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShamCashPaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending_review');

        $payments = ShamCashPayment::with(['invoice', 'patient'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->appends(['status' => $status]);

        $counts = [
            'pending_review' => ShamCashPayment::where('status', 'pending_review')->count(),
            'approved' => ShamCashPayment::where('status', 'approved')->count(),
            'rejected' => ShamCashPayment::where('status', 'rejected')->count(),
        ];

        return view('Dashboard.ShamCashPayments.index', compact('payments', 'status', 'counts'));
    }

    public function show(ShamCashPayment $shamCashPayment)
    {
        $shamCashPayment->load(['invoice.Doctor', 'invoice.Service', 'patient']);

        return view('Dashboard.ShamCashPayments.show', compact('shamCashPayment'));
    }

    public function approve(Request $request, ShamCashPayment $shamCashPayment)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:500']);

        try {
            ShamCashPaymentService::approve(
                $shamCashPayment,
                (int) Auth::guard('admin')->id(),
                $request->admin_notes
            );
            session()->flash('edit');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('sham-cash-payments.index');
    }

    public function reject(Request $request, ShamCashPayment $shamCashPayment)
    {
        $request->validate(['admin_notes' => 'nullable|string|max:500']);

        try {
            ShamCashPaymentService::reject(
                $shamCashPayment,
                (int) Auth::guard('admin')->id(),
                $request->admin_notes
            );
            session()->flash('delete');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('sham-cash-payments.index');
    }

    public function receipt(ShamCashPayment $shamCashPayment)
    {
        return Storage::disk('public')->response($shamCashPayment->receipt_path);
    }
}

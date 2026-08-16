<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::forAuthUser()->latest()->paginate(20);
        $unreadCount = NotificationService::unreadForAuth()->count();

        return view('Dashboard.Notifications.index', compact('notifications', 'unreadCount'));
    }

    public function read($id)
    {
        $notification = Notification::forAuthUser()
            ->where('id', $id)
            ->firstOrFail();

        $notification->reader_status = 1;
        $notification->save();

        $target = $notification->action_url ?: $this->inferActionUrl($notification->message);

        return redirect($target);
    }

    public function readAll()
    {
        NotificationService::unreadForAuth()->update(['reader_status' => 1]);

        return redirect()->route('notifications.index');
    }

    protected function inferActionUrl(string $message): string
    {
        if (str_contains($message, 'موعد') || str_contains($message, 'حجز')) {
            return route('appointments.index');
        }
        if (str_contains($message, 'إسعاف') || str_contains($message, 'اسعاف')) {
            return route('ambulance-requests.index');
        }
        if (str_contains($message, 'مراجعة') || str_contains($message, 'تقييم')) {
            return route('patient-testimonials.index', ['status' => 'pending']);
        }
        if (str_contains($message, 'فاتورة') || str_contains($message, 'كشف')) {
            return route('single_invoices');
        }
        if (str_contains($message, 'مطالبة') || str_contains($message, 'تأمين')) {
            return route('insurance-claims.index');
        }

        return route('notifications.index');
    }
}

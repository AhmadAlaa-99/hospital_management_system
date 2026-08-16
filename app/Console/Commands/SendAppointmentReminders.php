<?php

namespace App\Console\Commands;

use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send SMS/Email reminders 24 hours before confirmed appointments';

    public function handle()
    {
        $from = Carbon::now()->addHours(23);
        $to = Carbon::now()->addHours(25);

        $appointments = Appointment::where('type', 'مؤكد')
            ->where('reminder_sent', false)
            ->whereNotNull('appointment')
            ->whereBetween('appointment', [$from, $to])
            ->get();

        foreach ($appointments as $appointment) {
            $message = 'تذكير: موعدك غداً ' . $appointment->appointment . ' — ' . ($appointment->doctor->name ?? '');

            NotificationService::notifyPatientByEmail($appointment->email, $message);

            try {
                Mail::to($appointment->email)->send(
                    new AppointmentConfirmation($appointment->name, $appointment->appointment, true)
                );
            } catch (\Throwable $e) {
                // continue
            }

            $sid = env('TWILIO_SID');
            $token = env('TWILIO_TOKEN');
            $fromNumber = env('TWILIO_FROM');
            if ($sid && $token && $fromNumber && $appointment->phone) {
                try {
                    (new Client($sid, $token))->messages->create($appointment->phone, [
                        'from' => $fromNumber,
                        'body' => $message,
                    ]);
                } catch (\Throwable $e) {
                    // continue
                }
            }

            $appointment->update(['reminder_sent' => true]);
            $this->info('Reminder sent for appointment #' . $appointment->id);
        }

        return 0;
    }
}

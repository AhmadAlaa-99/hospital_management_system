<?php

namespace App\Console\Commands;

use App\Mail\ReviewReminder;
use App\Models\Diagnostic;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders';
    protected $description = 'Send SMS/Email reminders 24 hours before scheduled patient reviews';

    public function handle(): int
    {
        $from = Carbon::now()->addHours(23);
        $to = Carbon::now()->addHours(25);

        $diagnostics = Diagnostic::query()
            ->whereNotNull('review_date')
            ->where('review_reminder_sent', false)
            ->whereBetween('review_date', [$from, $to])
            ->whereHas('invoice', fn ($q) => $q->where('invoice_status', 2))
            ->with(['patient', 'Doctor', 'invoice'])
            ->get();

        foreach ($diagnostics as $diagnostic) {
            $patient = $diagnostic->patient;
            if (!$patient) {
                continue;
            }

            $patientName = $patient->name ?? 'المريض';
            $doctorName = optional($diagnostic->Doctor)->name;
            $reviewDate = Carbon::parse($diagnostic->review_date)->format('Y-m-d H:i');

            $message = 'تذكير: موعد مراجعتك ' . $reviewDate;
            if ($doctorName) {
                $message .= ' — ' . $doctorName;
            }

            if ($patient->email) {
                NotificationService::notifyPatient($patient->id, $message);

                try {
                    Mail::to($patient->email)->send(
                        new ReviewReminder($patientName, $reviewDate, $doctorName)
                    );
                } catch (\Throwable $e) {
                    // continue
                }
            }

            $sid = env('TWILIO_SID');
            $token = env('TWILIO_TOKEN');
            $fromNumber = env('TWILIO_FROM');
            if ($sid && $token && $fromNumber && $patient->Phone) {
                try {
                    (new Client($sid, $token))->messages->create($patient->Phone, [
                        'from' => $fromNumber,
                        'body' => $message,
                    ]);
                } catch (\Throwable $e) {
                    // continue
                }
            }

            $diagnostic->update(['review_reminder_sent' => true]);
            $this->info('Review reminder sent for diagnostic #' . $diagnostic->id);
        }

        return 0;
    }
}

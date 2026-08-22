<?php

namespace App\Console\Commands;

use App\Models\FollowUpPlan;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendFollowUpReminders extends Command
{
    protected $signature = 'followups:send-reminders';
    protected $description = 'Send reminders for upcoming follow-up plans';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $plans = FollowUpPlan::with('patient')
            ->where('status', 'scheduled')
            ->where('follow_up_date', $tomorrow)
            ->where('reminder_sent', false)
            ->get();

        foreach ($plans as $plan) {
            NotificationService::notifyPatient(
                (int) $plan->patient_id,
                'تذكير: لديك موعد متابعة غداً (' . $plan->follow_up_date->format('Y-m-d') . ')'
            );

            if ($plan->doctor_id) {
                NotificationService::notifyDoctor(
                    (int) $plan->doctor_id,
                    'تذكير: متابعة مريض #' . $plan->patient_id . ' غداً'
                );
            }

            $plan->update(['reminder_sent' => true]);
        }

        $this->info('Sent ' . $plans->count() . ' follow-up reminders.');
    }
}

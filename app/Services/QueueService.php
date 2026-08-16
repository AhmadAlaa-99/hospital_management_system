<?php

namespace App\Services;

use App\Events\QueueUpdated;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\QueueTicket;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueueService
{
    /** الحد الأقصى لأرقام الانتظار الصادرة يومياً لكل قسم */
    public const MAX_DAILY_TICKETS = 200;

    public function issueTicket(array $data): QueueTicket
    {
        $sectionId = (int) $data['section_id'];
        $doctorId = !empty($data['doctor_id']) ? (int) $data['doctor_id'] : null;
        $today = today()->toDateString();

        $this->validateDoctorSection($sectionId, $doctorId);

        if ($this->dailyIssuedCount($sectionId) >= self::MAX_DAILY_TICKETS) {
            throw new \RuntimeException('تم الوصول للحد الأقصى لأرقام الانتظار اليوم في هذا القسم');
        }

        $waitingCount = $this->waitingCount($sectionId, $doctorId);

        return DB::transaction(function () use ($data, $sectionId, $doctorId, $today, $waitingCount) {
            $sequence = (int) QueueTicket::where('section_id', $sectionId)
                ->whereDate('queue_date', $today)
                ->lockForUpdate()
                ->max('daily_sequence') + 1;

            $ticketNumber = $this->formatTicketNumber($sectionId, $sequence);
            $waitMinutes = $this->estimateWaitMinutes($sectionId, $doctorId, $waitingCount + 1);

            $ticket = QueueTicket::create([
                'ticket_number' => $ticketNumber,
                'daily_sequence' => $sequence,
                'queue_date' => $today,
                'section_id' => $sectionId,
                'doctor_id' => $doctorId,
                'appointment_id' => $data['appointment_id'] ?? null,
                'patient_id' => $data['patient_id'] ?? null,
                'patient_name' => $data['patient_name'],
                'phone' => $data['phone'] ?? null,
                'status' => 'waiting',
                'priority' => $data['priority'] ?? 'normal',
                'estimated_wait_minutes' => $waitMinutes,
                'issued_at' => now(),
            ]);

            $this->broadcastUpdate($sectionId, $doctorId);

            if ($doctorId) {
                NotificationService::notifyDoctor(
                    $doctorId,
                    'مريض جديد في قائمة الانتظار: ' . $ticket->ticket_number . ' — ' . $ticket->patient_name
                );
            }

            return $ticket;
        });
    }

    /**
     * @return array{ticket: QueueTicket, created: bool}
     */
    public function checkInAppointment(Appointment $appointment): array
    {
        if ($appointment->type !== 'مؤكد') {
            throw new \RuntimeException('يمكن تسجيل الحضور للمواعيد المؤكدة فقط');
        }

        if (!$appointment->appointment || !Carbon::parse($appointment->appointment)->isToday()) {
            throw new \RuntimeException('يمكن تسجيل الحضور لمواعيد اليوم فقط');
        }

        return DB::transaction(function () use ($appointment) {
            Appointment::whereKey($appointment->id)->lockForUpdate()->first();

            $existing = QueueTicket::today()
                ->where('appointment_id', $appointment->id)
                ->whereNotIn('status', ['cancelled', 'no_show'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return ['ticket' => $existing, 'created' => false];
            }

            $ticket = $this->issueTicket([
                'section_id' => $appointment->section_id,
                'doctor_id' => $appointment->doctor_id,
                'appointment_id' => $appointment->id,
                'patient_id' => optional(\App\Models\Patient::where('email', $appointment->email)->first())->id,
                'patient_name' => $appointment->name,
                'phone' => $appointment->phone,
                'priority' => 'normal',
            ]);

            return ['ticket' => $ticket, 'created' => true];
        });
    }

    public function callNext(int $sectionId, ?int $doctorId = null): ?QueueTicket
    {
        return DB::transaction(function () use ($sectionId, $doctorId) {
            $ticket = $this->nextWaitingTicketQuery($sectionId, $doctorId)
                ->lockForUpdate()
                ->first();

            if (!$ticket) {
                return null;
            }

            if ($doctorId) {
                QueueTicket::today()
                    ->where('section_id', $sectionId)
                    ->where('doctor_id', $doctorId)
                    ->where('status', 'called')
                    ->where('id', '!=', $ticket->id)
                    ->update(['status' => 'no_show', 'completed_at' => now()]);
            }

            $updates = [
                'status' => 'called',
                'called_at' => now(),
            ];

            if ($doctorId && !$ticket->doctor_id) {
                $updates['doctor_id'] = $doctorId;
            }

            $ticket->update($updates);

            $effectiveDoctorId = $doctorId ?? $ticket->doctor_id;
            $this->notifyPatientTurn($ticket->fresh());
            $this->broadcastUpdate($sectionId, $effectiveDoctorId);

            return $ticket->fresh(['section', 'doctor']);
        });
    }

    public function recall(QueueTicket $ticket): QueueTicket
    {
        $this->assertStatus($ticket, ['called', 'serving']);

        $ticket->update([
            'status' => 'called',
            'called_at' => now(),
        ]);

        $this->notifyPatientTurn($ticket);
        $this->broadcastUpdate($ticket->section_id, $ticket->doctor_id);

        return $ticket->fresh();
    }

    public function markServing(QueueTicket $ticket): QueueTicket
    {
        $this->assertStatus($ticket, ['called']);

        $ticket->update([
            'status' => 'serving',
            'serving_at' => now(),
        ]);

        $this->broadcastUpdate($ticket->section_id, $ticket->doctor_id);

        return $ticket->fresh();
    }

    public function complete(QueueTicket $ticket): QueueTicket
    {
        $this->assertStatus($ticket, ['serving']);

        $ticket->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->broadcastUpdate($ticket->section_id, $ticket->doctor_id);

        return $ticket->fresh();
    }

    public function noShow(QueueTicket $ticket): QueueTicket
    {
        $this->assertStatus($ticket, ['called']);

        $ticket->update([
            'status' => 'no_show',
            'completed_at' => now(),
        ]);

        $this->broadcastUpdate($ticket->section_id, $ticket->doctor_id);

        return $ticket->fresh();
    }

    public function cancel(QueueTicket $ticket): QueueTicket
    {
        $this->assertStatus($ticket, ['waiting', 'called', 'serving']);

        $ticket->update(['status' => 'cancelled', 'completed_at' => now()]);
        $this->broadcastUpdate($ticket->section_id, $ticket->doctor_id);

        return $ticket->fresh();
    }

    public function getDisplayData(int $sectionId, ?int $doctorId = null): array
    {
        $base = QueueTicket::with(['doctor', 'section'])
            ->today()
            ->where('section_id', $sectionId);

        if ($doctorId) {
            $base->where(function ($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId)->orWhereNull('doctor_id');
            });
        }

        $current = (clone $base)->whereIn('status', ['called', 'serving'])
            ->orderByRaw("FIELD(status, 'serving', 'called')")
            ->orderByDesc('serving_at')
            ->orderByDesc('called_at')
            ->first();

        $waiting = (clone $base)->where('status', 'waiting')
            ->orderByRaw("FIELD(priority, 'urgent', 'elderly', 'normal')")
            ->orderBy('daily_sequence')
            ->limit(8)
            ->get();

        $waiting->each(function (QueueTicket $ticket, int $index) use ($sectionId, $doctorId) {
            $ticket->estimated_wait_minutes = $this->estimateWaitMinutes(
                $sectionId,
                $doctorId ?? $ticket->doctor_id,
                $index + 1
            );
        });

        $recent = (clone $base)->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->limit(5)
            ->get();

        return [
            'current' => $current,
            'waiting' => $waiting,
            'recent' => $recent,
            'waiting_count' => (clone $base)->where('status', 'waiting')->count(),
            'section' => Section::find($sectionId),
        ];
    }

    public function trackTicket(string $ticketNumber): ?QueueTicket
    {
        return QueueTicket::with(['section', 'doctor'])
            ->today()
            ->where('ticket_number', strtoupper(trim($ticketNumber)))
            ->first();
    }

    protected function nextWaitingTicketQuery(int $sectionId, ?int $doctorId)
    {
        $query = QueueTicket::today()
            ->where('section_id', $sectionId)
            ->where('status', 'waiting');

        if ($doctorId) {
            $query->where(function ($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId)->orWhereNull('doctor_id');
            });
        }

        return $query
            ->orderByRaw("FIELD(priority, 'urgent', 'elderly', 'normal')")
            ->orderBy('daily_sequence');
    }

    protected function dailyIssuedCount(int $sectionId): int
    {
        return QueueTicket::today()
            ->where('section_id', $sectionId)
            ->whereNotIn('status', ['cancelled'])
            ->count();
    }

    protected function waitingCount(int $sectionId, ?int $doctorId): int
    {
        $query = QueueTicket::today()
            ->where('section_id', $sectionId)
            ->where('status', 'waiting');

        if ($doctorId) {
            $query->where(function ($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId)->orWhereNull('doctor_id');
            });
        }

        return $query->count();
    }

    protected function validateDoctorSection(int $sectionId, ?int $doctorId): void
    {
        if (!$doctorId) {
            return;
        }

        $valid = Doctor::where('id', $doctorId)
            ->where('section_id', $sectionId)
            ->where('status', 1)
            ->exists();

        if (!$valid) {
            throw new \RuntimeException('الطبيب غير تابع لهذا القسم أو غير مفعّل');
        }
    }

    protected function assertStatus(QueueTicket $ticket, array $allowed): void
    {
        if (!in_array($ticket->status, $allowed, true)) {
            $label = QueueTicket::$statusLabels[$ticket->status] ?? $ticket->status;
            throw new \RuntimeException('لا يمكن تنفيذ العملية — الحالة الحالية: ' . $label);
        }
    }

    protected function estimateWaitMinutes(int $sectionId, ?int $doctorId, int $position): int
    {
        $slotDuration = 15;

        if ($doctorId) {
            $dayOfWeek = (int) now()->dayOfWeek;
            $schedule = DoctorSchedule::where('doctor_id', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->first();
            if ($schedule) {
                $slotDuration = max(10, (int) $schedule->slot_duration);
            }
        }

        return max(0, ($position - 1) * $slotDuration);
    }

    protected function formatTicketNumber(int $sectionId, int $sequence): string
    {
        $code = $this->sectionTicketCode($sectionId);

        return $code . '-' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    public function sectionTicketCode(int $sectionId): string
    {
        $section = Section::find($sectionId);
        if (!$section || !$section->name) {
            return 'S' . str_pad((string) $sectionId, 2, '0', STR_PAD_LEFT);
        }

        $name = $section->name;

        if (str_contains($name, 'قلب') || str_contains($name, 'أوعية')) {
            return 'CARD';
        }
        if (str_contains($name, 'مخ') || str_contains($name, 'عصب') || str_contains($name, 'دماغ')) {
            return 'NEUR';
        }
        if (str_contains($name, 'أطفال') || str_contains($name, 'اطفال')) {
            return 'PED';
        }
        if (str_contains($name, 'عيون') || str_contains($name, 'عين')) {
            return 'EYE';
        }
        if (str_contains($name, 'جراح')) {
            return 'SURG';
        }
        if (str_contains($name, 'باطن') || str_contains($name, 'هضم')) {
            return 'INT';
        }

        return 'S' . str_pad((string) $sectionId, 2, '0', STR_PAD_LEFT);
    }

    public function sectionTicketLabel(int $sectionId): string
    {
        $map = [
            'CARD' => 'القلب',
            'NEUR' => 'المخ والأعصاب',
            'PED' => 'الأطفال',
            'EYE' => 'العيون',
            'SURG' => 'الجراحة',
            'INT' => 'الباطنة',
        ];

        $code = $this->sectionTicketCode($sectionId);

        return $map[$code] ?? (optional(Section::find($sectionId))->name ?? 'القسم');
    }

    public function parseTicketDisplay(string $ticketNumber): array
    {
        if (strpos($ticketNumber, '-') !== false) {
            [$code, $number] = explode('-', $ticketNumber, 2);

            return [
                'code' => $code,
                'number' => $number,
                'full' => $ticketNumber,
            ];
        }

        return ['code' => '', 'number' => $ticketNumber, 'full' => $ticketNumber];
    }

    protected function notifyPatientTurn(QueueTicket $ticket): void
    {
        $ticket->loadMissing(['appointment', 'patient']);
        $message = 'حان دورك! رقم ' . $ticket->ticket_number . ' — يرجى التوجه للعيادة';

        $email = optional($ticket->appointment)->email
            ?? optional($ticket->patient)->email;

        if ($email) {
            NotificationService::notifyPatientByEmail($email, $message);
        }

        $accountSid = env('TWILIO_SID');
        $authToken = env('TWILIO_TOKEN');
        $fromNumber = env('TWILIO_FROM');

        if ($accountSid && $authToken && $fromNumber && $ticket->phone) {
            try {
                $client = new \Twilio\Rest\Client($accountSid, $authToken);
                $client->messages->create($ticket->phone, [
                    'from' => $fromNumber,
                    'body' => $message,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Queue SMS failed: ' . $e->getMessage());
            }
        }
    }

    protected function broadcastUpdate(int $sectionId, ?int $doctorId): void
    {
        try {
            event(new QueueUpdated(
                $sectionId,
                $doctorId,
                $this->serializeDisplayData($this->getDisplayData($sectionId, $doctorId))
            ));
        } catch (\Throwable $e) {
            Log::warning('Queue broadcast failed: ' . $e->getMessage());
        }
    }

    public function serializeDisplayData(array $data): array
    {
        return [
            'current' => $data['current'] ? $this->serializeTicket($data['current']) : null,
            'waiting' => $data['waiting']->map(fn ($t) => $this->serializeTicket($t))->values()->all(),
            'recent' => $data['recent']->map(fn ($t) => $this->serializeTicket($t))->values()->all(),
            'waiting_count' => $data['waiting_count'],
            'section' => $data['section']
                ? ['id' => $data['section']->id, 'name' => $data['section']->name]
                : null,
        ];
    }

    protected function serializeTicket(QueueTicket $ticket): array
    {
        $display = $this->parseTicketDisplay($ticket->ticket_number);

        return [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'display_code' => $display['code'],
            'display_number' => $display['number'],
            'section_label' => $this->sectionTicketLabel($ticket->section_id),
            'patient_name' => $ticket->patient_name,
            'status' => $ticket->status,
            'status_label' => QueueTicket::$statusLabels[$ticket->status] ?? $ticket->status,
            'priority' => $ticket->priority,
            'priority_label' => QueueTicket::$priorityLabels[$ticket->priority] ?? $ticket->priority,
            'estimated_wait_minutes' => $ticket->estimated_wait_minutes,
            'doctor' => $ticket->doctor ? ['id' => $ticket->doctor->id, 'name' => $ticket->doctor->name] : null,
        ];
    }
}

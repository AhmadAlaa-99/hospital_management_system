<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\QueueTicket;
use App\Models\Section;
use App\Services\QueueService;
use Illuminate\Database\Seeder;

class QueueTicketSeeder extends Seeder
{
    public function run()
    {
        QueueTicket::whereDate('queue_date', today())->delete();

        $sections = Section::all();
        if ($sections->isEmpty()) {
            return;
        }

        /** @var QueueService $queue */
        $queue = app(QueueService::class);

        $scenarios = [
            [
                ['name' => 'أحمد محمود الشامي', 'phone' => '0593111001', 'priority' => 'normal', 'status' => 'completed'],
                ['name' => 'سارة خليل الحلبي', 'phone' => '0593111002', 'priority' => 'normal', 'status' => 'completed'],
                ['name' => 'عمر حسن بركات', 'phone' => '0593111003', 'priority' => 'elderly', 'status' => 'serving'],
                ['name' => 'ليلى كمال ناصر', 'phone' => '0593111004', 'priority' => 'normal', 'status' => 'waiting'],
                ['name' => 'ياسر نبيل الصالح', 'phone' => '0593111005', 'priority' => 'urgent', 'status' => 'waiting'],
                ['name' => 'نور الدين الحسين', 'phone' => '0593111006', 'priority' => 'normal', 'status' => 'waiting'],
                ['name' => 'ريم فادي الأسعد', 'phone' => '0593111007', 'priority' => 'normal', 'status' => 'waiting'],
                ['name' => 'كمال عيسى زيدان', 'phone' => '0593111008', 'priority' => 'normal', 'status' => 'waiting'],
            ],
            [
                ['name' => 'فادي سعيد', 'phone' => '0593222001', 'priority' => 'normal', 'status' => 'completed'],
                ['name' => 'ميساء روحي', 'phone' => '0593222002', 'priority' => 'normal', 'status' => 'completed'],
                ['name' => 'باسم خضر', 'phone' => '0593222003', 'priority' => 'normal', 'status' => 'called'],
                ['name' => 'جنى وليد', 'phone' => '0593222004', 'priority' => 'elderly', 'status' => 'waiting'],
                ['name' => 'أنس طارق', 'phone' => '0593222005', 'priority' => 'normal', 'status' => 'waiting'],
                ['name' => 'هبة منذر', 'phone' => '0593222006', 'priority' => 'urgent', 'status' => 'waiting'],
            ],
            [
                ['name' => 'طفل: يوسف علي', 'phone' => '0593333001', 'priority' => 'normal', 'status' => 'completed'],
                ['name' => 'طفل: ليان محمد', 'phone' => '0593333002', 'priority' => 'normal', 'status' => 'serving'],
                ['name' => 'طفل: زينب أحمد', 'phone' => '0593333003', 'priority' => 'normal', 'status' => 'waiting'],
                ['name' => 'طفل: كريم نادر', 'phone' => '0593333004', 'priority' => 'urgent', 'status' => 'waiting'],
                ['name' => 'طفل: هنا نور', 'phone' => '0593333005', 'priority' => 'normal', 'status' => 'waiting'],
            ],
        ];

        foreach ($sections as $index => $section) {
            $doctor = Doctor::where('section_id', $section->id)->where('status', 1)->first();
            if (!$doctor) {
                continue;
            }

            foreach ($scenarios[$index % count($scenarios)] as $data) {
                $ticket = $queue->issueWalkInTicket([
                    'section_id' => $section->id,
                    'doctor_id' => $doctor->id,
                    'patient_name' => $data['name'],
                    'phone' => $data['phone'],
                    'priority' => $data['priority'],
                ]);

                $this->applyStatus($ticket, $data['status']);
            }
        }
    }

    protected function applyStatus(QueueTicket $ticket, string $status): void
    {
        $updates = ['status' => $status];

        if (in_array($status, ['called', 'serving', 'completed'], true)) {
            $updates['called_at'] = now()->subMinutes(rand(5, 30));
        }
        if (in_array($status, ['serving', 'completed'], true)) {
            $updates['serving_at'] = now()->subMinutes(rand(3, 15));
        }
        if ($status === 'completed') {
            $updates['completed_at'] = now()->subMinutes(rand(1, 10));
        }

        $ticket->update($updates);
    }
}

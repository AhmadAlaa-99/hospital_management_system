<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Conversation;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Patient;
use App\Models\QueueTicket;
use App\Services\QueueService;

$doctor = Doctor::where('email', 'doctor@gmail.com')->first();
if (!$doctor) {
    echo "Doctor not found\n";
    exit(1);
}

$doctor->password = \Illuminate\Support\Facades\Hash::make('12345678');
$doctor->save();

Message::query()->delete();
Conversation::query()->delete();

$patients = Patient::take(3)->get();
$dialogues = [
    [
        ['patient', 'السلام عليكم دكتور، أريد استفساراً عن نتائج الفحص'],
        ['doctor', 'وعليكم السلام، النتائج جيدة بشكل عام وسأشرحها في الموعد'],
        ['patient', 'شكراً دكتور، هل أحتاج متابعة بعد أسبوع؟'],
        ['doctor', 'نعم، يرجى حجز موعد متابعة بعد 7 أيام'],
    ],
    [
        ['patient', 'دكتور، هل يمكن تأجيل موعد الغد؟'],
        ['doctor', 'نعم، اختر وقتاً من صفحة حجز المواعيد'],
        ['patient', 'تمام، سأحجز يوم الأحد'],
    ],
    [
        ['patient', 'السلام عليكم، متى تتوفر نتيجة الأشعة؟'],
        ['doctor', 'ستصلك إشعاراً فور رفعها من قسم الأشعة'],
        ['patient', 'شكراً جزيلاً'],
        ['doctor', 'العفو، لا تتردد بالسؤال'],
    ],
];

foreach ($patients as $i => $patient) {
    $conv = Conversation::create([
        'sender_email' => $patient->email,
        'receiver_email' => $doctor->email,
        'last_time_message' => now()->subMinutes(10 - $i),
    ]);

    foreach ($dialogues[$i] as $j => [$from, $body]) {
        $senderEmail = $from === 'doctor' ? $doctor->email : $patient->email;
        $receiverEmail = $from === 'doctor' ? $patient->email : $doctor->email;
        Message::create([
            'conversation_id' => $conv->id,
            'sender_email' => $senderEmail,
            'receiver_email' => $receiverEmail,
            'body' => $body,
            'read' => 1,
            'created_at' => now()->subMinutes(40 - $j * 5),
            'updated_at' => now()->subMinutes(40 - $j * 5),
        ]);
    }

    $conv->update(['last_time_message' => now()->subMinutes(2 + $i)]);
}

/** @var QueueService $queue */
$queue = app(QueueService::class);
QueueTicket::where('doctor_id', $doctor->id)->whereDate('queue_date', today())->delete();

$rows = [
    ['name' => 'أحمد محمود الشامي', 'phone' => '0593111001', 'priority' => 'normal', 'status' => 'completed'],
    ['name' => 'سارة خليل الحلبي', 'phone' => '0593111002', 'priority' => 'normal', 'status' => 'completed'],
    ['name' => 'عمر حسن بركات', 'phone' => '0593111003', 'priority' => 'elderly', 'status' => 'serving'],
    ['name' => 'ليلى كمال ناصر', 'phone' => '0593111004', 'priority' => 'normal', 'status' => 'waiting'],
    ['name' => 'ياسر نبيل الصالح', 'phone' => '0593111005', 'priority' => 'urgent', 'status' => 'waiting'],
    ['name' => 'نور الدين الحسين', 'phone' => '0593111006', 'priority' => 'normal', 'status' => 'waiting'],
    ['name' => 'ريم فادي الأسعد', 'phone' => '0593111007', 'priority' => 'normal', 'status' => 'waiting'],
];

foreach ($rows as $data) {
    $ticket = $queue->issueTicket([
        'section_id' => $doctor->section_id,
        'doctor_id' => $doctor->id,
        'patient_name' => $data['name'],
        'phone' => $data['phone'],
        'priority' => $data['priority'],
    ]);

    $updates = ['status' => $data['status']];
    if (in_array($data['status'], ['called', 'serving', 'completed'], true)) {
        $updates['called_at'] = now()->subMinutes(rand(5, 30));
    }
    if (in_array($data['status'], ['serving', 'completed'], true)) {
        $updates['serving_at'] = now()->subMinutes(rand(3, 15));
    }
    if ($data['status'] === 'completed') {
        $updates['completed_at'] = now()->subMinutes(rand(1, 10));
    }
    $ticket->update($updates);
}

echo "OK doctor={$doctor->id} conversations=" . Conversation::count()
    . " tickets=" . QueueTicket::where('doctor_id', $doctor->id)->whereDate('queue_date', today())->count() . "\n";

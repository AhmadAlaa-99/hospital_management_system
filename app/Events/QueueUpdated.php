<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sectionId;
    public $doctorId;
    public $payload;

    public function __construct(int $sectionId, ?int $doctorId, array $payload)
    {
        $this->sectionId = $sectionId;
        $this->doctorId = $doctorId;
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        $channels = [new Channel('queue.section.' . $this->sectionId)];
        if ($this->doctorId) {
            $channels[] = new Channel('queue.doctor.' . $this->doctorId);
        }
        return $channels;
    }

    public function broadcastAs()
    {
        return 'queue-updated';
    }
}

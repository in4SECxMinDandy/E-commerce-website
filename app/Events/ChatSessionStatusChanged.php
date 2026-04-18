<?php

namespace App\Events;

use App\Models\ChatSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatSessionStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatSession $session)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat.session.'.$this->session->id.'.'.$this->session->visit_token),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.session.status';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->session->id,
            'status' => $this->session->status->value,
            'closed_at' => $this->session->closed_at?->toIso8601String(),
            'last_message_at' => $this->session->last_message_at?->toIso8601String(),
        ];
    }
}

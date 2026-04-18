<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ChatMessageCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
        //
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat.session.'.$this->message->session->id.'.'.$this->message->session->visit_token),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'session_id' => $this->message->session_id,
            'sender_role' => $this->message->sender_role->value,
            'sender_label' => $this->message->sender_role->label(),
            'content' => $this->message->content,
            'image_url' => $this->message->image_path
                ? Storage::disk('public')->url($this->message->image_path)
                : null,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}

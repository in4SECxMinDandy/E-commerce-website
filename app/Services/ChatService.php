<?php

namespace App\Services;

use App\Enums\ChatSenderRole;
use App\Enums\ChatSessionStatus;
use App\Enums\ChatSessionType;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\User;
use App\Models\VisitSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatService
{
    public function resolveSession(
        ?User $user,
        ?VisitSession $visitSession,
        ?string $guestUuid,
        ?string $guestName
    ): ChatSession {
        $query = ChatSession::query()->where('status', ChatSessionStatus::Open);

        if ($user) {
            $query->where('user_id', $user->id);
        } elseif ($guestUuid && $visitSession) {
            $query
                ->where('guest_uuid', $guestUuid)
                ->where('visit_session_id', $visitSession->id);
        } elseif ($guestUuid) {
            $query->where('guest_uuid', $guestUuid);
        } elseif ($visitSession) {
            $query->where('visit_session_id', $visitSession->id);
        }

        $session = $query->latest('last_message_at')->first();

        if ($session) {
            if ($guestName && $session->guest_name !== $guestName) {
                $session->forceFill(['guest_name' => $guestName])->save();
            }

            return $session;
        }

        return ChatSession::create([
            'user_id' => $user?->id,
            'guest_uuid' => $guestUuid,
            'guest_name' => $guestName,
            'visit_session_id' => $visitSession?->id,
            'visit_token' => $visitSession?->token ?? Str::random(40),
            'status' => ChatSessionStatus::Open,
            'session_type' => $user ? ChatSessionType::User : ChatSessionType::Guest,
            'opened_at' => now(),
            'last_message_at' => now(),
        ]);
    }

    public function sendMessage(
        ChatSession $session,
        ChatSenderRole $senderRole,
        ?User $user,
        ?string $guestUuid,
        ?string $content = null,
        ?string $imagePath = null
    ): ChatMessage {
        return DB::transaction(function () use ($session, $senderRole, $user, $guestUuid, $content, $imagePath): ChatMessage {
            $message = ChatMessage::create([
                'session_id' => $session->id,
                'user_id' => $user?->id,
                'guest_uuid' => $guestUuid,
                'sender_role' => $senderRole,
                'content' => $content,
                'image_path' => $imagePath,
                'is_read' => false,
            ]);

            $session->forceFill([
                'status' => ChatSessionStatus::Open,
                'last_message_at' => $message->created_at,
                'closed_at' => null,
            ])->save();

            return $message;
        });
    }

    public function closeSession(ChatSession $session): void
    {
        $session->forceFill([
            'status' => ChatSessionStatus::Closed,
            'closed_at' => now(),
        ])->save();
    }
}

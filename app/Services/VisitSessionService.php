<?php

namespace App\Services;

use App\Enums\ChatSessionStatus;
use App\Enums\VisitSessionStatus;
use App\Models\ChatSession;
use App\Models\VisitSession;
use Illuminate\Support\Collection;

class VisitSessionService
{
    public function syncExpiredStatuses(): void
    {
        $expiredIds = VisitSession::query()
            ->where('status', VisitSessionStatus::Active)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->pluck('id');

        if ($expiredIds->isEmpty()) {
            return;
        }

        VisitSession::query()
            ->whereKey($expiredIds)
            ->update(['status' => VisitSessionStatus::Expired->value]);

        ChatSession::query()
            ->whereIn('visit_session_id', $expiredIds)
            ->where('status', ChatSessionStatus::Open)
            ->update([
                'status' => ChatSessionStatus::Closed->value,
                'closed_at' => now(),
            ]);
    }

    public function syncStatus(VisitSession $visitSession): VisitSession
    {
        if (
            $visitSession->status === VisitSessionStatus::Active
            && $visitSession->expires_at
            && $visitSession->expires_at->isPast()
        ) {
            $visitSession->forceFill(['status' => VisitSessionStatus::Expired])->save();
            $this->closeOpenChatSessions($visitSession);
        }

        return $visitSession->refresh();
    }

    public function isAccessible(VisitSession $visitSession): bool
    {
        return $visitSession->status === VisitSessionStatus::Active
            && (! $visitSession->expires_at || $visitSession->expires_at->isFuture());
    }

    /**
     * @return Collection<int, ChatSession>
     */
    public function closeOpenChatSessions(VisitSession $visitSession): Collection
    {
        $openSessions = $visitSession->chatSessions()
            ->where('status', ChatSessionStatus::Open)
            ->get();

        foreach ($openSessions as $session) {
            $session->forceFill([
                'status' => ChatSessionStatus::Closed,
                'closed_at' => now(),
            ])->save();
        }

        return $openSessions;
    }
}

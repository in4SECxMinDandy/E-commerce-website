<?php

namespace App\Http\Controllers;

use App\Enums\ChatSenderRole;
use App\Events\ChatMessageCreated;
use App\Events\ChatSessionStatusChanged;
use App\Http\Requests\Chat\SendChatMessageRequest;
use App\Http\Requests\Chat\UploadChatImageRequest;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Models\VisitSession;
use App\Services\ChatService;
use App\Services\VisitSessionService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService,
        private readonly VisitSessionService $visitSessionService,
    ) {
        //
    }

    public function show(Request $request): View
    {
        $user = $request->user();
        $visitSession = null;
        $guestUuid = null;
        $guestName = null;

        if (! $user) {
            $visitToken = $request->query('visit_token', session(config('universaltea.chat.visit_token_session_key')));

            if (! $visitToken) {
                return view('chat.entry');
            }

            $visitSession = VisitSession::query()
                ->where('token', $visitToken)
                ->firstOrFail();

            $visitSession = $this->visitSessionService->syncStatus($visitSession);

            abort_if(
                ! $this->visitSessionService->isAccessible($visitSession),
                403,
                'PhiÃªn QR Ä‘Ã£ háº¿t háº¡n hoáº·c bá»‹ vÃ´ hiá»‡u hÃ³a.',
            );

            $guestUuid = session(config('universaltea.chat.guest_uuid_session_key')) ?: (string) Str::uuid();
            $guestName = $request->string('guest_name')->trim()->toString()
                ?: session(config('universaltea.chat.guest_name_session_key'))
                ?: 'KhÃ¡ch QR';

            session([
                config('universaltea.chat.visit_token_session_key') => $visitToken,
                config('universaltea.chat.guest_uuid_session_key') => $guestUuid,
                config('universaltea.chat.guest_name_session_key') => $guestName,
            ]);

            $visitSession->forceFill(['last_used_at' => now()])->save();
        }

        $chatSession = $this->chatService->resolveSession($user, $visitSession, $guestUuid, $guestName);
        $messages = $chatSession->messages()
            ->latest()
            ->take(config('universaltea.chat.recent_messages_page_size'))
            ->get()
            ->reverse()
            ->values();

        return view('chat.index', [
            'chatSession' => $chatSession,
            'messages' => $messages,
            'pollingSeconds' => config('universaltea.chat.polling_interval_seconds'),
            'guestName' => $guestName,
            'channelName' => 'chat.session.'.$chatSession->id.'.'.$chatSession->visit_token,
        ]);
    }

    public function messages(Request $request): JsonResponse
    {
        $session = ChatSession::query()->with('visitSession')->findOrFail($request->integer('session_id'));
        $this->authorizeSession($request, $session);

        $query = $session->messages()->orderByDesc('id');

        if ($request->filled('cursor')) {
            $query->where('id', '<', $request->integer('cursor'));
        }

        $messages = $query
            ->take(config('universaltea.chat.recent_messages_page_size'))
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $message) => $this->formatMessage($message));

        return response()->json([
            'data' => $messages,
            'next_cursor' => $messages->isNotEmpty() ? $messages->first()['id'] : null,
            'status' => $session->status->value,
        ]);
    }

    public function send(SendChatMessageRequest $request): JsonResponse
    {
        $session = ChatSession::query()->with('visitSession')->findOrFail($request->integer('session_id'));
        $this->authorizeSession($request, $session);

        $message = $this->chatService->sendMessage(
            $session,
            $request->user() ? ChatSenderRole::User : ChatSenderRole::Guest,
            $request->user(),
            session(config('universaltea.chat.guest_uuid_session_key')),
            $request->string('content')->trim()->toString() ?: null,
            $request->string('image_path')->toString() ?: null,
        );

        $this->broadcastSafely(new ChatMessageCreated($message));

        return response()->json([
            'data' => $this->formatMessage($message),
        ]);
    }

    public function uploadImage(UploadChatImageRequest $request): JsonResponse
    {
        $session = ChatSession::query()->with('visitSession')->findOrFail($request->integer('session_id'));
        $this->authorizeSession($request, $session);

        $actor = $request->user() ? 'user' : 'guest';
        $file = $request->file('image');
        $path = $file->storeAs(
            'chat-images/'.$actor,
            (string) Str::uuid().'.'.$file->guessExtension(),
            'public',
        );

        return response()->json([
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    private function authorizeSession(Request $request, ChatSession $session): void
    {
        if ($request->user()) {
            abort_unless(
                $session->user_id === $request->user()->id || $request->user()->hasRole(config('universaltea.roles.admin')),
                403
            );

            return;
        }

        abort_unless(
            $session->guest_uuid === session(config('universaltea.chat.guest_uuid_session_key'))
            && $session->visit_token === session(config('universaltea.chat.visit_token_session_key')),
            403
        );

        if (! $session->visitSession) {
            return;
        }

        $visitSession = $this->visitSessionService->syncStatus($session->visitSession);

        if ($this->visitSessionService->isAccessible($visitSession)) {
            return;
        }

        $this->chatService->closeSession($session);
        $this->broadcastSafely(new ChatSessionStatusChanged($session->refresh()));

        if ($request->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => 'PhiÃªn chat Ä‘Ã£ háº¿t háº¡n hoáº·c bá»‹ vÃ´ hiá»‡u hÃ³a.',
                'status' => 'closed',
            ], 403));
        }

        abort(403, 'PhiÃªn chat Ä‘Ã£ háº¿t háº¡n hoáº·c bá»‹ vÃ´ hiá»‡u hÃ³a.');
    }

    private function formatMessage(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_role' => $message->sender_role->value,
            'sender_label' => $message->sender_role->label(),
            'content' => $message->content,
            'image_url' => $message->image_path ? Storage::disk('public')->url($message->image_path) : null,
            'is_read' => $message->is_read,
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }
}

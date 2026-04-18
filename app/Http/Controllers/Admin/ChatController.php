<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ChatSenderRole;
use App\Events\ChatMessageCreated;
use App\Events\ChatSessionStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\SendChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(private readonly ChatService $chatService)
    {
        //
    }

    public function index(Request $request): View
    {
        $sessions = ChatSession::query()
            ->withCount('messages')
            ->latest('last_message_at')
            ->paginate(20)
            ->withQueryString();

        $selectedSession = $request->filled('session')
            ? ChatSession::query()->with('messages')->find($request->integer('session'))
            : $sessions->first();

        return view('admin.chat.index', [
            'sessions' => $sessions,
            'selectedSession' => $selectedSession?->load('messages'),
        ]);
    }

    public function reply(SendChatMessageRequest $request, ChatSession $session): RedirectResponse
    {
        $message = $this->chatService->sendMessage(
            $session,
            ChatSenderRole::Admin,
            $request->user(),
            null,
            $request->string('content')->trim()->toString() ?: null,
            $request->string('image_path')->toString() ?: null,
        );

        $this->broadcastSafely(new ChatMessageCreated($message));

        return back()->with('status', 'Đã gửi phản hồi.');
    }

    public function close(ChatSession $session): JsonResponse
    {
        $this->chatService->closeSession($session);
        $this->broadcastSafely(new ChatSessionStatusChanged($session->refresh()));

        return response()->json([
            'status' => $session->status->value,
        ]);
    }

    public function destroy(ChatSession $session): RedirectResponse
    {
        $session->delete();

        return redirect()->route('admin.chat.index')->with('status', 'Đã xóa session chat.');
    }

    public function destroyMessage(ChatSession $session, ChatMessage $message): RedirectResponse
    {
        abort_unless($message->session_id === $session->id, 404);

        $message->delete();

        return back()->with('status', 'Đã xóa tin nhắn.');
    }
}

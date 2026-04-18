<?php

namespace App\Http\Controllers\Admin;

use App\Enums\VisitSessionStatus;
use App\Events\ChatSessionStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVisitSessionRequest;
use App\Models\VisitSession;
use App\Services\QrCodeService;
use App\Services\VisitSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VisitSessionController extends Controller
{
    public function __construct(
        private readonly QrCodeService $qrCodeService,
        private readonly VisitSessionService $visitSessionService,
    ) {
        //
    }

    public function index(): View
    {
        $this->visitSessionService->syncExpiredStatuses();

        $visitSessions = VisitSession::query()
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $qrSvgs = [];
        $chatUrls = [];

        foreach ($visitSessions as $visitSession) {
            $chatUrl = route('chat.show', ['visit_token' => $visitSession->token]);

            $chatUrls[$visitSession->id] = $chatUrl;
            $qrSvgs[$visitSession->id] = $this->qrCodeService->svg($chatUrl);
        }

        return view('admin.visit-sessions.index', [
            'visitSessions' => $visitSessions,
            'qrSvgs' => $qrSvgs,
            'chatUrls' => $chatUrls,
        ]);
    }

    public function store(StoreVisitSessionRequest $request): RedirectResponse
    {
        VisitSession::create([
            'label' => $request->string('label'),
            'token' => Str::random(40),
            'status' => VisitSessionStatus::Active,
            'expires_at' => $request->date('expires_at'),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('status', 'Đã tạo QR visit session.');
    }

    public function disable(VisitSession $visitSession): RedirectResponse
    {
        $visitSession->forceFill(['status' => VisitSessionStatus::Disabled])->save();

        foreach ($this->visitSessionService->closeOpenChatSessions($visitSession) as $session) {
            $this->broadcastSafely(new ChatSessionStatusChanged($session));
        }

        return back()->with('status', 'Đã vô hiệu hóa visit session.');
    }

    public function destroy(VisitSession $visitSession): RedirectResponse
    {
        foreach ($this->visitSessionService->closeOpenChatSessions($visitSession) as $session) {
            $this->broadcastSafely(new ChatSessionStatusChanged($session));
        }

        $visitSession->delete();

        return back()->with('status', 'Đã xóa visit session.');
    }
}

<?php

use App\Enums\ChatSessionStatus;
use App\Enums\ChatSessionType;
use App\Enums\VisitSessionStatus;
use App\Models\ChatSession;
use App\Models\VisitSession;

test('guest scanning a new qr resolves a chat session for that visit session', function () {
    $firstVisitSession = VisitSession::create([
        'label' => 'First QR',
        'token' => 'first-visit-token',
        'status' => VisitSessionStatus::Active,
        'expires_at' => now()->addHour(),
    ]);

    $secondVisitSession = VisitSession::create([
        'label' => 'Second QR',
        'token' => 'second-visit-token',
        'status' => VisitSessionStatus::Active,
        'expires_at' => now()->addHour(),
    ]);

    $existingSession = ChatSession::create([
        'guest_uuid' => 'shared-guest-uuid',
        'guest_name' => 'Guest',
        'visit_session_id' => $firstVisitSession->id,
        'visit_token' => $firstVisitSession->token,
        'status' => ChatSessionStatus::Open,
        'session_type' => ChatSessionType::Guest,
        'opened_at' => now()->subMinutes(5),
        'last_message_at' => now()->subMinute(),
    ]);

    $response = $this
        ->withSession([
            config('universaltea.chat.guest_uuid_session_key') => 'shared-guest-uuid',
            config('universaltea.chat.visit_token_session_key') => $firstVisitSession->token,
            config('universaltea.chat.guest_name_session_key') => 'Guest',
        ])
        ->get(route('chat.show', ['visit_token' => $secondVisitSession->token]));

    $response->assertOk()
        ->assertViewHas('chatSession', fn (ChatSession $chatSession) => $chatSession->visit_session_id === $secondVisitSession->id
            && $chatSession->id !== $existingSession->id);

    expect(ChatSession::query()
        ->where('visit_session_id', $secondVisitSession->id)
        ->where('guest_uuid', 'shared-guest-uuid')
        ->exists())->toBeTrue();
});

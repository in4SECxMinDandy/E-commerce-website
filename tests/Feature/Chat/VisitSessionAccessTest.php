<?php

use App\Enums\ChatSessionStatus;
use App\Enums\ChatSessionType;
use App\Enums\VisitSessionStatus;
use App\Models\ChatSession;
use App\Models\VisitSession;

test('guest can not open chat with an expired visit token', function () {
    $visitSession = VisitSession::create([
        'label' => 'Expired QR',
        'token' => 'expired-token',
        'status' => VisitSessionStatus::Active,
        'expires_at' => now()->subMinute(),
    ]);

    $response = $this->get(route('chat.show', ['visit_token' => $visitSession->token]));

    $response->assertForbidden();
    expect($visitSession->refresh()->status)->toBe(VisitSessionStatus::Expired);
});

test('guest message polling is blocked when the linked visit session expires', function () {
    $visitSession = VisitSession::create([
        'label' => 'Soon Expired QR',
        'token' => 'soon-expired-token',
        'status' => VisitSessionStatus::Active,
        'expires_at' => now()->subMinute(),
    ]);

    $chatSession = ChatSession::create([
        'guest_uuid' => 'guest-uuid-1',
        'guest_name' => 'Guest',
        'visit_session_id' => $visitSession->id,
        'visit_token' => $visitSession->token,
        'status' => ChatSessionStatus::Open,
        'session_type' => ChatSessionType::Guest,
        'opened_at' => now()->subMinutes(5),
        'last_message_at' => now()->subMinute(),
    ]);

    $response = $this
        ->withSession([
            config('universaltea.chat.guest_uuid_session_key') => 'guest-uuid-1',
            config('universaltea.chat.visit_token_session_key') => $visitSession->token,
        ])
        ->getJson(route('chat.messages', ['session_id' => $chatSession->id]));

    $response->assertForbidden()
        ->assertJson([
            'status' => 'closed',
        ]);

    expect($visitSession->refresh()->status)->toBe(VisitSessionStatus::Expired);
    expect($chatSession->refresh()->status)->toBe(ChatSessionStatus::Closed);
});

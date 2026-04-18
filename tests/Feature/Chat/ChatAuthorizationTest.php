<?php

use App\Enums\ChatSessionStatus;
use App\Enums\ChatSessionType;
use App\Enums\VisitSessionStatus;
use App\Models\ChatSession;
use App\Models\VisitSession;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function fakePngUpload(string $name): UploadedFile
{
    $pngHeader = hex2bin('89504E470D0A1A0A0000000D4948445200000001000000010802000000907753DE0000000C49444154789C636001000000FFFF03000006000557BFAB0000000049454E44AE426082');

    return UploadedFile::fake()->createWithContent($name, $pngHeader);
}

test('guest must own the chat session to upload an image', function () {
    Storage::fake('public');

    $visitSession = VisitSession::create([
        'label' => 'QR A',
        'token' => 'visit-token-a',
        'status' => VisitSessionStatus::Active,
        'expires_at' => now()->addHour(),
    ]);

    $chatSession = ChatSession::create([
        'guest_uuid' => 'guest-uuid-a',
        'guest_name' => 'Guest A',
        'visit_session_id' => $visitSession->id,
        'visit_token' => $visitSession->token,
        'status' => ChatSessionStatus::Open,
        'session_type' => ChatSessionType::Guest,
        'opened_at' => now(),
        'last_message_at' => now(),
    ]);

    $response = $this
        ->withSession([
            config('universaltea.chat.guest_uuid_session_key') => 'guest-uuid-b',
            config('universaltea.chat.visit_token_session_key') => 'visit-token-b',
        ])
        ->post(route('chat.upload-image'), [
            'session_id' => $chatSession->id,
            'image' => fakePngUpload('unauthorized.png'),
        ]);

    $response->assertForbidden();
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

test('guest can upload an image for the active owned chat session', function () {
    Storage::fake('public');

    $visitSession = VisitSession::create([
        'label' => 'QR B',
        'token' => 'visit-token-owned',
        'status' => VisitSessionStatus::Active,
        'expires_at' => now()->addHour(),
    ]);

    $chatSession = ChatSession::create([
        'guest_uuid' => 'guest-uuid-owned',
        'guest_name' => 'Guest Owned',
        'visit_session_id' => $visitSession->id,
        'visit_token' => $visitSession->token,
        'status' => ChatSessionStatus::Open,
        'session_type' => ChatSessionType::Guest,
        'opened_at' => now(),
        'last_message_at' => now(),
    ]);

    $response = $this
        ->withSession([
            config('universaltea.chat.guest_uuid_session_key') => 'guest-uuid-owned',
            config('universaltea.chat.visit_token_session_key') => $visitSession->token,
        ])
        ->post(route('chat.upload-image'), [
            'session_id' => $chatSession->id,
            'image' => fakePngUpload('authorized.png'),
        ]);

    $response->assertOk()
        ->assertJsonStructure(['path', 'url']);

    expect(Storage::disk('public')->exists($response->json('path')))->toBeTrue();
});

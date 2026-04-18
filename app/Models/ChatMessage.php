<?php

namespace App\Models;

use App\Enums\ChatSenderRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'guest_uuid',
        'sender_role',
        'content',
        'image_path',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'sender_role' => ChatSenderRole::class,
            'is_read' => 'boolean',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

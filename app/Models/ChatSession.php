<?php

namespace App\Models;

use App\Enums\ChatSessionStatus;
use App\Enums\ChatSessionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_uuid',
        'guest_name',
        'visit_session_id',
        'visit_token',
        'status',
        'session_type',
        'opened_at',
        'closed_at',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ChatSessionStatus::class,
            'session_type' => ChatSessionType::class,
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'last_message_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function visitSession(): BelongsTo
    {
        return $this->belongsTo(VisitSession::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }
}

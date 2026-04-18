<?php

namespace App\Models;

use App\Enums\VisitSessionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'token',
        'status',
        'expires_at',
        'last_used_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => VisitSessionStatus::class,
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function isAccessible(): bool
    {
        return $this->status === VisitSessionStatus::Active
            && (! $this->expires_at || $this->expires_at->isFuture());
    }
}

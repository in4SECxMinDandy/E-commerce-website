<?php

namespace App\Enums;

enum ChatSenderRole: string
{
    case User = 'user';
    case Guest = 'guest';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::User => 'Khách hàng',
            self::Guest => 'Khách QR',
            self::Admin => 'Admin',
        };
    }
}

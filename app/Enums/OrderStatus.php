<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Preparing = 'preparing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xử lý',
            self::Confirmed => 'Đã xác nhận',
            self::Preparing => 'Đang chuẩn bị',
            self::Completed => 'Đã hoàn thành',
            self::Cancelled => 'Đã hủy',
            self::Refunded => 'Đã hoàn tiền',
        };
    }
}

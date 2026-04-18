<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Food;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OrderService
{
    public function placeOrder(User $user, Food $food, int $quantity, ?string $note = null): Order
    {
        $order = DB::transaction(function () use ($user, $food, $quantity, $note): Order {
            $lockedFood = Food::query()
                ->lockForUpdate()
                ->findOrFail($food->id);

            if (! $lockedFood->is_available) {
                throw new RuntimeException('Món ăn hiện không khả dụng.');
            }

            if ($lockedFood->stock < $quantity) {
                throw new RuntimeException("Bạn chỉ có thể đặt tối đa {$lockedFood->stock} sản phẩm.");
            }

            $remainingStock = $lockedFood->stock - $quantity;

            $lockedFood->forceFill([
                'stock' => $remainingStock,
                'is_available' => $remainingStock > 0,
            ])->save();

            return Order::create([
                'user_id' => $user->id,
                'food_id' => $lockedFood->id,
                'quantity' => $quantity,
                'note' => $note,
                'unit_price' => $lockedFood->price,
                'total_price' => round(((float) $lockedFood->price) * $quantity, 2),
                'status' => OrderStatus::Pending,
                'ordered_at' => now(),
            ]);
        });

        event(new \App\Events\OrderCreated($order));

        return $order;
    }
}

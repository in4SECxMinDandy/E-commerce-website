<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Food;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * API Order Controller
 * Dựa trên everything-claude-code skills:
 * - api-design: REST conventions, error handling patterns
 * - laravel-security: auth validation, rate limiting
 * - php/patterns: clean error responses
 */
class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
        //
    }

    /**
     * POST /api/orders
     * Tạo đơn hàng qua AJAX. Yêu cầu đăng nhập.
     * Trả về JSON response với success/error structure.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $food = Food::query()->find($validated['food_id']);

        if (! $food) {
            return response()->json([
                'success' => false,
                'message' => 'Món không tồn tại.',
                'errors' => ['food_id' => ['Món không tồn tại.']],
            ], 422);
        }

        try {
            $order = $this->orderService->placeOrder(
                $request->user(),
                $food,
                $validated['quantity'],
                $validated['note'] ?? null,
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã tạo đơn hàng #' . $order->id . ' thành công.',
                'data' => [
                    'order_id' => $order->id,
                    'food_name' => $food->name,
                    'quantity' => $order->quantity,
                    'total_price' => $order->total_price,
                    'status' => $order->status->value,
                ],
            ], 201);
        } catch (RuntimeException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'errors' => ['quantity' => [$exception->getMessage()]],
            ], 422);
        }
    }
}

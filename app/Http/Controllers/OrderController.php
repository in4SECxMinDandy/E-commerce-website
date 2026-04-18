<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Food;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
        //
    }

    public function store(StoreOrderRequest $request): RedirectResponse|JsonResponse
    {
        $food = Food::query()->findOrFail($request->integer('food_id'));

        try {
            $order = $this->orderService->placeOrder(
                $request->user(),
                $food,
                $request->integer('quantity'),
                $request->string('note')->toString() ?: null,
            );
        } catch (RuntimeException $exception) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'errors' => [
                        'quantity' => [$exception->getMessage()],
                    ],
                ], 422);
            }

            return back()->withInput()->withErrors([
                'quantity' => $exception->getMessage(),
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã tạo đơn hàng #'.$order->id.' thành công.',
                'data' => [
                    'order_id' => $order->id,
                    'food_name' => $food->name,
                    'quantity' => $order->quantity,
                    'total_price' => $order->total_price,
                    'status' => $order->status->value,
                ],
            ], 201);
        }

        return redirect()
            ->route('history')
            ->with('status', 'Đã tạo đơn hàng #'.$order->id.' thành công.');
    }

    public function history(Request $request): View
    {
        $orders = Order::query()
            ->with('food')
            ->where('user_id', $request->user()->id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest('ordered_at')
            ->paginate(10)
            ->withQueryString();

        return view('orders.history', [
            'orders' => $orders,
        ]);
    }
}

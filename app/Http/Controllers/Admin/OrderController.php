<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::query()
            ->with(['food', 'user'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest('ordered_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function update(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $status = OrderStatus::from($request->string('status')->toString());

        $order->forceFill([
            'status' => $status,
            'processed_at' => in_array($status, [OrderStatus::Confirmed, OrderStatus::Preparing], true) ? now() : $order->processed_at,
            'completed_at' => $status === OrderStatus::Completed ? now() : null,
            'cancelled_at' => in_array($status, [OrderStatus::Cancelled, OrderStatus::Refunded], true) ? now() : null,
        ])->save();

        return back()->with('status', 'Đã cập nhật trạng thái đơn hàng.');
    }
}

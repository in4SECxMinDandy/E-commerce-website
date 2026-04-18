<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatSession;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\Order;
use App\Models\VisitSession;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'foods' => Food::count(),
                'categories' => FoodCategory::count(),
                'orders' => Order::count(),
                'openChats' => ChatSession::query()->where('status', 'open')->count(),
                'visitSessions' => VisitSession::count(),
            ],
        ]);
    }
}

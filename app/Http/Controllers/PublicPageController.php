<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'featuredFoods' => Food::query()
                ->with('category')
                ->where('is_available', true)
                ->where('is_featured', true)
                ->latest()
                ->take(6)
                ->get(),
            'categories' => FoodCategory::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }
}

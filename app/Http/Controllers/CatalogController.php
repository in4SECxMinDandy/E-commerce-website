<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $categorySlug = $request->string('category')->toString();

        $foods = Food::query()
            ->with('category')
            ->where('is_available', true)
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($subQuery) use ($request) {
                    $keyword = $request->string('q')->trim()->toString();

                    $subQuery
                        ->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('description', 'like', '%'.$keyword.'%');
                });
            })
            ->when($categorySlug, function ($query) use ($categorySlug) {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $categorySlug));
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('foods.index', [
            'foods' => $foods,
            'categories' => FoodCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'activeCategory' => $categorySlug,
        ]);
    }

    public function show(Food $food): View
    {
        abort_unless($food->is_available, 404);

        return view('foods.show', [
            'food' => $food->load('category'),
            'relatedFoods' => Food::query()
                ->where('category_id', $food->category_id)
                ->whereKeyNot($food->id)
                ->where('is_available', true)
                ->take(4)
                ->get(),
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * API Food Controller
 * Dựa trên everything-claude-code skills:
 * - api-design: REST conventions, consistent JSON structure
 * - laravel-patterns: thin controllers, query optimization
 * - php/patterns: DTOs, clean return types
 */
class FoodController extends Controller
{
    /**
     * GET /api/foods
     * Lấy danh sách foods với filter (category, search) và phân trang.
     * Trả về JSON cho AJAX requests hoặc server-rendered HTML tùy Accept header.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'q' => ['nullable', 'string', 'max:200'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $categorySlug = $request->query('category');
        $keyword = $request->query('q');

        $foods = Food::query()
            ->with('category')
            ->where('is_available', true)
            ->when($keyword, function ($query) use ($keyword): void {
                $query->where(function ($subQuery) use ($keyword): void {
                    $subQuery
                        ->where('name', 'like', '%' . $keyword . '%')
                        ->orWhere('description', 'like', '%' . $keyword . '%');
                });
            })
            ->when($categorySlug, function ($query) use ($categorySlug): void {
                $query->whereHas(
                    'category',
                    fn($categoryQuery) => $categoryQuery->where('slug', $categorySlug)
                );
            })
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = FoodCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'success' => true,
            'data' => $foods->items(),
            'meta' => [
                'current_page' => $foods->currentPage(),
                'last_page' => $foods->lastPage(),
                'per_page' => $foods->perPage(),
                'total' => $foods->total(),
            ],
            'filters' => [
                'category' => $categorySlug,
                'q' => $keyword,
            ],
            'categories' => $categories,
        ]);
    }

    /**
     * GET /api/foods/{slug}
     * Lấy chi tiết một food với related foods.
     */
    public function show(Food $food): JsonResponse
    {
        abort_unless($food->is_available, 404, 'Món không tồn tại hoặc đã ngừng phục vụ.');

        $food->load('category');

        $relatedFoods = Food::query()
            ->where('category_id', $food->category_id)
            ->whereKeyNot($food->id)
            ->where('is_available', true)
            ->take(4)
            ->get(['id', 'name', 'slug', 'price']);

        return response()->json([
            'success' => true,
            'data' => $food,
            'related_foods' => $relatedFoods,
        ]);
    }
}

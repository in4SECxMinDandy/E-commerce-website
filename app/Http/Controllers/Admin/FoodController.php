<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFoodRequest;
use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FoodController extends Controller
{
    public function index(): View
    {
        return view('admin.foods.index', [
            'foods' => Food::query()->with('category')->latest()->paginate(15),
            'categories' => FoodCategory::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(StoreFoodRequest $request): RedirectResponse
    {
        Food::create($this->payloadFromRequest($request));

        return back()->with('status', 'Đã tạo món mới.');
    }

    public function edit(Food $food): View
    {
        return view('admin.foods.edit', [
            'food' => $food,
            'categories' => FoodCategory::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(StoreFoodRequest $request, Food $food): RedirectResponse
    {
        $food->update($this->payloadFromRequest($request, $food));

        return redirect()->route('admin.foods.index')->with('status', 'Đã cập nhật món ăn.');
    }

    public function destroy(Food $food): RedirectResponse
    {
        $food->delete();

        return back()->with('status', 'Đã xóa món ăn.');
    }

    private function payloadFromRequest(StoreFoodRequest $request, ?Food $food = null): array
    {
        $imagePath = $food?->image_path;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->storeAs(
                'food-images',
                (string) Str::uuid().'.'.$request->file('image')->guessExtension(),
                'public',
            );
        }

        return [
            'category_id' => $request->integer('category_id'),
            'name' => $request->string('name'),
            'slug' => Str::slug($request->string('name')),
            'short_description' => $request->string('short_description')->toString() ?: null,
            'description' => $request->string('description')->toString() ?: null,
            'price' => $request->input('price'),
            'stock' => $request->integer('stock'),
            'is_available' => $request->boolean('is_available'),
            'is_featured' => $request->boolean('is_featured'),
            'image_path' => $imagePath,
        ];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFoodCategoryRequest;
use App\Models\FoodCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FoodCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => FoodCategory::query()->orderBy('sort_order')->paginate(15),
        ]);
    }

    public function store(StoreFoodCategoryRequest $request): RedirectResponse
    {
        FoodCategory::create([
            'name' => $request->string('name'),
            'slug' => Str::slug($request->string('name')),
            'description' => $request->string('description')->toString() ?: null,
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('status', 'Đã tạo danh mục mới.');
    }

    public function edit(FoodCategory $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(StoreFoodCategoryRequest $request, FoodCategory $category): RedirectResponse
    {
        $category->update([
            'name' => $request->string('name'),
            'slug' => Str::slug($request->string('name')),
            'description' => $request->string('description')->toString() ?: null,
            'sort_order' => $request->integer('sort_order'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.categories.index')->with('status', 'Đã cập nhật danh mục.');
    }

    public function destroy(FoodCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('status', 'Đã xóa danh mục.');
    }
}

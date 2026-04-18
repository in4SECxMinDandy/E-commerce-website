<?php

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createAdminUser(): User
{
    $adminRole = Role::findOrCreate(config('universaltea.roles.admin'));
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);

    return $admin;
}

test('admin can create an unavailable food when the checkbox is omitted', function () {
    $admin = createAdminUser();
    $category = FoodCategory::create([
        'name' => 'Milk Tea',
        'slug' => 'milk-tea',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response = $this
        ->from(route('admin.foods.index'))
        ->actingAs($admin)
        ->post(route('admin.foods.store'), [
            'category_id' => $category->id,
            'name' => 'Unavailable Tea',
            'price' => 42000,
            'stock' => 12,
        ]);

    $response
        ->assertRedirect(route('admin.foods.index'))
        ->assertSessionHas('status', 'Đã tạo món mới.');

    expect(Food::query()->where('slug', 'unavailable-tea')->firstOrFail()->is_available)->toBeFalse();
});

test('admin can mark an existing food as unavailable by unchecking the checkbox', function () {
    $admin = createAdminUser();
    $category = FoodCategory::create([
        'name' => 'Fruit Tea',
        'slug' => 'fruit-tea',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $food = Food::create([
        'category_id' => $category->id,
        'name' => 'Berry Tea',
        'slug' => 'berry-tea',
        'price' => 39000,
        'stock' => 8,
        'is_available' => true,
        'is_featured' => false,
    ]);

    $response = $this
        ->actingAs($admin)
        ->put(route('admin.foods.update', $food), [
            'category_id' => $category->id,
            'name' => $food->name,
            'price' => $food->price,
            'stock' => $food->stock,
        ]);

    $response->assertRedirect(route('admin.foods.index'));

    expect($food->refresh()->is_available)->toBeFalse();
});

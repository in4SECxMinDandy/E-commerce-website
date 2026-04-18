<?php

use App\Enums\OrderStatus;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\User;
use Spatie\Permission\Models\Role;

function createOrderAdmin(): User
{
    $adminRole = Role::findOrCreate(config('universaltea.roles.admin'));
    $admin = User::factory()->create();
    $admin->assignRole($adminRole);

    return $admin;
}

test('placing an order that exhausts stock marks the food unavailable', function () {
    $user = User::factory()->create();
    $category = FoodCategory::create([
        'name' => 'Signature Tea',
        'slug' => 'signature-tea',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $food = Food::create([
        'category_id' => $category->id,
        'name' => 'Royal Milk Tea',
        'slug' => 'royal-milk-tea',
        'price' => 45000,
        'stock' => 2,
        'is_available' => true,
        'is_featured' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('orders.store'), [
            'food_id' => $food->id,
            'quantity' => 2,
        ]);

    $response->assertRedirect(route('history'));

    $food->refresh();

    expect($food->stock)->toBe(0);
    expect($food->is_available)->toBeFalse();

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'food_id' => $food->id,
        'quantity' => 2,
        'status' => OrderStatus::Pending->value,
    ]);
});

test('user cannot place an order with quantity greater than available stock', function () {
    $user = User::factory()->create();
    $category = FoodCategory::create([
        'name' => 'Seasonal Tea',
        'slug' => 'seasonal-tea',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $food = Food::create([
        'category_id' => $category->id,
        'name' => 'Peach Oolong',
        'slug' => 'peach-oolong',
        'price' => 49000,
        'stock' => 2,
        'is_available' => true,
        'is_featured' => false,
    ]);

    $response = $this
        ->from(route('foods.show', $food))
        ->actingAs($user)
        ->post(route('orders.store'), [
            'food_id' => $food->id,
            'quantity' => 3,
        ]);

    $response
        ->assertRedirect(route('foods.show', $food))
        ->assertSessionHasErrors([
            'quantity' => 'Bạn chỉ có thể đặt tối đa 2 sản phẩm.',
        ]);

    expect($food->refresh()->stock)->toBe(2);

    $this->assertDatabaseMissing('orders', [
        'user_id' => $user->id,
        'food_id' => $food->id,
        'quantity' => 3,
    ]);
});

test('authenticated user can place an order via ajax and the order is visible in history and admin', function () {
    $user = User::factory()->create([
        'full_name' => 'Nguyen Van A',
    ]);
    $admin = createOrderAdmin();
    $category = FoodCategory::create([
        'name' => 'Fruit Tea',
        'slug' => 'fruit-tea',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $food = Food::create([
        'category_id' => $category->id,
        'name' => 'Peach Tea',
        'slug' => 'peach-tea',
        'price' => 38000,
        'stock' => 5,
        'is_available' => true,
        'is_featured' => false,
    ]);

    $response = $this
        ->actingAs($user)
        ->postJson(route('orders.store'), [
            'food_id' => $food->id,
            'quantity' => 2,
            'note' => 'Less ice',
        ]);

    $response
        ->assertCreated()
        ->assertJson([
            'success' => true,
            'data' => [
                'food_name' => 'Peach Tea',
                'quantity' => 2,
                'status' => OrderStatus::Pending->value,
            ],
        ]);

    expect($food->refresh()->stock)->toBe(3);
    expect($food->is_available)->toBeTrue();

    $orderId = $response->json('data.order_id');

    $this->actingAs($user)
        ->get(route('history'))
        ->assertOk()
        ->assertSee('#'.$orderId)
        ->assertSee('Peach Tea')
        ->assertSee(OrderStatus::Pending->label());

    $this->actingAs($admin)
        ->get(route('admin.orders.index'))
        ->assertOk()
        ->assertSee('Nguyen Van A')
        ->assertSee('Peach Tea')
        ->assertSee(OrderStatus::Pending->label());
});

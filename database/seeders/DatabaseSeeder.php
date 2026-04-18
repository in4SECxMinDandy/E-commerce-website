<?php

namespace Database\Seeders;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => config('universaltea.roles.admin')]);
        $customerRole = Role::firstOrCreate(['name' => config('universaltea.roles.customer')]);

        $admin = User::firstOrCreate([
            'email' => 'admin@universaltea.test',
        ], [
            'full_name' => 'Universal Tea Admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole($adminRole);

        $user = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'full_name' => 'Test User',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $user->assignRole($customerRole);

        $category = FoodCategory::firstOrCreate([
            'slug' => 'tra-sua',
        ], [
            'name' => 'Trà sữa',
            'description' => 'Danh mục mặc định cho bản clone Laravel.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Food::firstOrCreate([
            'slug' => 'tra-sua-tran-chau-hoang-gia',
        ], [
            'category_id' => $category->id,
            'name' => 'Trà sữa trân châu hoàng gia',
            'short_description' => 'Món demo cho trang chủ và catalog.',
            'description' => 'Dữ liệu mẫu được tạo từ seeder để kiểm tra flow foundation.',
            'price' => 45000,
            'stock' => 50,
            'is_available' => true,
            'is_featured' => true,
        ]);
    }
}

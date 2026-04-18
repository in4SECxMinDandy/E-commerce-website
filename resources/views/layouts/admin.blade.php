{{--
    File frontend: layouts/admin.blade.php
    Chuc nang: Layout khung cho toan bo khu quan tri.
    Vai tro giao dien: Dinh nghia sidebar menu admin, nap asset SCSS/JS chung, hien alerts va vung yield cho noi dung con.
    Tuong tac: Dieu huong menu dua tren route hien tai va hien thong tin nguoi dang nhap o thanh ben.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Quản trị | Universal Tea' }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-shell">
    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-12 col-lg-3 col-xl-2 border-end bg-dark-subtle p-4">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-dark">
                    <div class="fs-4 fw-bold text-uppercase">Universal Tea</div>
                    <div class="small text-secondary">Bảng điều khiển quản trị</div>
                </a>

                <nav class="nav flex-column gap-2 mt-4">
                    <a class="nav-link px-0 {{ request()->routeIs('admin.dashboard') ? 'fw-semibold text-dark' : 'text-secondary' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <a class="nav-link px-0 {{ request()->routeIs('admin.foods.*') ? 'fw-semibold text-dark' : 'text-secondary' }}" href="{{ route('admin.foods.index') }}">Món ăn</a>
                    <a class="nav-link px-0 {{ request()->routeIs('admin.categories.*') ? 'fw-semibold text-dark' : 'text-secondary' }}" href="{{ route('admin.categories.index') }}">Danh mục</a>
                    <a class="nav-link px-0 {{ request()->routeIs('admin.orders.*') ? 'fw-semibold text-dark' : 'text-secondary' }}" href="{{ route('admin.orders.index') }}">Đơn hàng</a>
                    <a class="nav-link px-0 {{ request()->routeIs('admin.chat.*') ? 'fw-semibold text-dark' : 'text-secondary' }}" href="{{ route('admin.chat.index') }}">Chat</a>
                    <a class="nav-link px-0 {{ request()->routeIs('admin.visit-sessions.*') ? 'fw-semibold text-dark' : 'text-secondary' }}" href="{{ route('admin.visit-sessions.index') }}">Phiên QR</a>
                    <a class="nav-link px-0 text-secondary" href="{{ route('home') }}">Trang công khai</a>
                </nav>

                <div class="mt-4 small text-secondary">
                    Đăng nhập với {{ auth()->user()->full_name }}
                </div>
            </aside>

            <div class="col-12 col-lg-9 col-xl-10 p-4 p-lg-5">
                @include('partials.alerts')
                @yield('content')
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>


{{--
    File frontend: layouts/base.blade.php
    Chuc nang: Layout cong khai chinh cua Universal Tea ban Blade + Bootstrap.
    Vai tro giao dien: Dinh nghia khung HTML, nap asset, thanh dieu huong public, nut auth/admin, vung alerts va noi dung trang con.
    Tuong tac: Dieu kien hien menu theo trang thai dang nhap va role admin, dong thoi danh dau link active theo route hien tai.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Universal Tea') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-body-tertiary">
    <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top shadow-sm">
        <div class="container py-2">
            <a class="navbar-brand fw-bold text-uppercase tracking-wide" href="{{ route('home') }}">
                Universal Tea
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-controls="siteNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="siteNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('foods.*') ? 'active' : '' }}" href="{{ route('foods.index') }}">Thực phẩm</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Giới thiệu</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}" href="{{ route('chat.show') }}">Chat</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}" href="{{ route('history') }}">Lịch sử</a></li>
                    @endauth
                </ul>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @auth
                        @if (auth()->user()->hasRole(config('universaltea.roles.admin')))
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('admin.dashboard') }}">Quản trị</a>
                        @endif
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('profile.edit') }}">{{ auth()->user()->full_name }}</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-dark" type="submit">Đăng xuất</button>
                        </form>
                    @else
                        <a class="btn btn-sm btn-outline-dark" href="{{ route('login') }}">Đăng nhập</a>
                        <a class="btn btn-sm btn-dark" href="{{ route('register') }}">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-4 py-lg-5">
        <div class="container">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>


{{--
    File frontend: auth/login.blade.php
    Chuc nang: Trang dang nhap cho nguoi dung thuong.
    Vai tro giao dien: Mo khoa cac tinh nang can phien dang nhap nhu dat mon, xem lich su don va tham gia chat user-side.
    Tuong tac: Form dang nhap, tuy chon remember me va lien ket den trang quen mat khau, dang ky.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h2 fw-bold mb-3">Đăng nhập</h1>
                    <p class="text-secondary">Sử dụng tài khoản khách hàng để đặt món, xem lịch sử và vào chat.</p>

                    <form method="POST" action="{{ route('login') }}" class="vstack gap-3 mt-4">
                        @csrf
                        <div>
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div>
                            <label class="form-label" for="password">Mật khẩu</label>
                            <input class="form-control" id="password" type="password" name="password" required>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                        <button class="btn btn-dark w-100" type="submit">Đăng nhập</button>
                    </form>

                    <div class="d-flex justify-content-between align-items-center mt-4 small">
                        <a href="{{ route('password.request') }}">Quên mật khẩu?</a>
                        <a href="{{ route('register') }}">Tạo tài khoản mới</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


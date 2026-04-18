{{--
    File frontend: auth/register.blade.php
    Chuc nang: Trang tao tai khoan nguoi dung moi.
    Vai tro giao dien: Thu thap thong tin co ban de nguoi dung co the su dung order history va chat trong he thong.
    Tuong tac: Form dang ky gom ho ten, email, mat khau, xac nhan mat khau va dieu huong nguoc lai trang login.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-9 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h2 fw-bold mb-3">Đăng ký tài khoản</h1>
                    <p class="text-secondary">Tài khoản này được dùng cho order history và chat user-side.</p>

                    <form method="POST" action="{{ route('register') }}" class="row g-3 mt-2">
                        @csrf
                        <div class="col-12">
                            <label class="form-label" for="full_name">Họ và tên</label>
                            <input class="form-control" id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" required autofocus>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password">Mật khẩu</label>
                            <input class="form-control" id="password" type="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Xác nhận mật khẩu</label>
                            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <a class="small" href="{{ route('login') }}">Đã có tài khoản?</a>
                            <button class="btn btn-dark" type="submit">Đăng ký</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


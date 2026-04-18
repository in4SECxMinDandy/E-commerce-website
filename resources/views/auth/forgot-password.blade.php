{{--
    File frontend: auth/forgot-password.blade.php
    Chuc nang: Trang yeu cau gui email dat lai mat khau.
    Vai tro giao dien: Cho phep nguoi dung nhap email de bat dau luong phuc hoi tai khoan.
    Tuong tac: Form POST den route password.email; UI don gian, tap trung vao mot hanh dong gui lien ket reset.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold mb-3">Quên mật khẩu</h1>
                    <p class="text-secondary">Nhập email để nhận liên kết đặt lại mật khẩu.</p>

                    <form method="POST" action="{{ route('password.email') }}" class="vstack gap-3 mt-4">
                        @csrf
                        <div>
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <button class="btn btn-dark" type="submit">Gửi liên kết reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


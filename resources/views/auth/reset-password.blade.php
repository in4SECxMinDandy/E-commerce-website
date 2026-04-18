{{--
    File frontend: auth/reset-password.blade.php
    Chuc nang: Trang dat lai mat khau sau khi nguoi dung mo lien ket tu email.
    Vai tro giao dien: Hoan tat buoc cuoi cua luong khoi phuc tai khoan, bao gom token, email va mat khau moi.
    Tuong tac: Form gui token an, email va cap mat khau moi den route password.store.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-9 col-xl-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold mb-3">Đặt lại mật khẩu</h1>

                    <form method="POST" action="{{ route('password.store') }}" class="row g-3 mt-2">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div class="col-12">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password">Mật khẩu mới</label>
                            <input class="form-control" id="password" type="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="password_confirmation">Xác nhận mật khẩu</label>
                            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-dark" type="submit">Cập nhật mật khẩu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


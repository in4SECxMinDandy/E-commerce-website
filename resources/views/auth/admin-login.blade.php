{{--
    File frontend: auth/admin-login.blade.php
    Chuc nang: Trang dang nhap rieng cho tai khoan admin.
    Vai tro giao dien: Tach diem vao khu quan tri khoi dang nhap khach hang thong thuong, nhung van su dung cung session auth cua Laravel.
    Tuong tac: Form gui email va mat khau den route admin.login.store, giao dien toi gian de tap trung vao xac thuc.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-dark mb-3">Admin access</span>
                    <h1 class="h2 fw-bold mb-3">Đăng nhập admin</h1>
                    <p class="text-secondary">Route riêng cho admin nhưng vẫn sử dụng chung session backend của Laravel.</p>

                    <form method="POST" action="{{ route('admin.login.store') }}" class="vstack gap-3 mt-4">
                        @csrf
                        <div>
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div>
                            <label class="form-label" for="password">Mật khẩu</label>
                            <input class="form-control" id="password" type="password" name="password" required>
                        </div>
                        <button class="btn btn-dark w-100" type="submit">Vào admin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


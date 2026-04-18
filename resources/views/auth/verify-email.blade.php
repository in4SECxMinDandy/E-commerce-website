{{--
    File frontend: auth/verify-email.blade.php
    Chuc nang: Nhac nguoi dung xac minh dia chi email va cho phep gui lai email kich hoat.
    Vai tro giao dien: Dung de giu trang thai trung gian sau dang ky neu tai khoan bat buoc verify email.
    Tuong tac: Co hai form nho: gui lai email verify va dang xuat khoi phien hien tai.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold mb-3">Xác minh email</h1>
                    <p class="text-secondary">
                        Trước khi tiếp tục, vui lòng kiểm tra email của bạn để xác minh địa chỉ email. Nếu bạn chưa nhận được, hãy gửi lại liên kết.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button class="btn btn-dark" type="submit">Gửi lại email xác minh</button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-secondary" type="submit">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


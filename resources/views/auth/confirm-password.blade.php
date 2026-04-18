{{--
    File frontend: auth/confirm-password.blade.php
    Chuc nang: Xac nhan lai mat khau truoc khi truy cap thao tac nhay cam.
    Vai tro giao dien: Them lop bao ve UX truoc cac hanh dong can bao mat cao nhu xoa tai khoan hoac sua du lieu quan trong.
    Tuong tac: Form nhan mot truong password va gui den route xac nhan mat khau cua Laravel.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-xl-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold mb-3">Xác nhận mật khẩu</h1>
                    <p class="text-secondary">Vì khu vực này nhạy cảm, vui lòng nhập lại mật khẩu của bạn.</p>

                    <form method="POST" action="{{ route('password.confirm') }}" class="vstack gap-3 mt-4">
                        @csrf
                        <div>
                            <label class="form-label" for="password">Mật khẩu</label>
                            <input class="form-control" id="password" type="password" name="password" required autofocus>
                        </div>
                        <button class="btn btn-dark" type="submit">Xác nhận</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


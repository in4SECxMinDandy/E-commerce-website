{{--
    File frontend: profile/edit.blade.php
    Chuc nang: Trang ho so Bootstrap da duoc viet lai cho du an hien tai.
    Vai tro giao dien: Gom ba khoi nghiep vu tren cung mot man hinh: cap nhat thong tin ca nhan, doi mat khau va xoa tai khoan.
    Tuong tac: Moi khoi co form rieng gui den cac route profile/password/destroy tuong ung.
--}}
@extends('layouts.base')

@section('content')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h3 fw-bold mb-4">Cập nhật hồ sơ</h1>

                    <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                        @csrf
                        @method('PATCH')
                        <div class="col-12">
                            <label class="form-label" for="full_name">Họ và tên</label>
                            <input class="form-control" id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-dark" type="submit">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Đổi mật khẩu</h2>
                    <form method="POST" action="{{ route('password.update') }}" class="vstack gap-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="form-label" for="current_password">Mật khẩu hiện tại</label>
                            <input class="form-control" id="current_password" type="password" name="current_password" required>
                        </div>
                        <div>
                            <label class="form-label" for="password">Mật khẩu mới</label>
                            <input class="form-control" id="password" type="password" name="password" required>
                        </div>
                        <div>
                            <label class="form-label" for="password_confirmation">Xác nhận mật khẩu mới</label>
                            <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required>
                        </div>
                        <button class="btn btn-outline-dark" type="submit">Cập nhật mật khẩu</button>
                    </form>
                </div>
            </div>

            <div class="card border border-danger-subtle rounded-4">
                <div class="card-body p-4">
                    <h2 class="h5 text-danger mb-3">Xóa tài khoản</h2>
                    <form method="POST" action="{{ route('profile.destroy') }}" class="vstack gap-3">
                        @csrf
                        @method('DELETE')
                        <div>
                            <label class="form-label" for="delete_password">Nhập mật khẩu để xác nhận</label>
                            <input class="form-control" id="delete_password" type="password" name="password" required>
                        </div>
                        <button class="btn btn-danger" type="submit">Xóa tài khoản</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


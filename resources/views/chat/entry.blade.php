{{--
    File frontend: chat/entry.blade.php
    Chuc nang: Trang huong dan truoc khi vao guest chat.
    Vai tro giao dien: Giai thich cach khach quet QR visit session de nhan token va khoi tao phien chat, dong thoi dua ra cac lua chon tiep theo.
    Tuong tac: Chu yeu la cac nut dieu huong sang dang nhap hoac trang gioi thieu he thong.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-dark mb-3">Guest chat</span>
                    <h1 class="display-6 fw-bold mb-3">Để vào guest chat, bạn cần quét mã QR visit session hoặc đăng nhập bằng tài khoản thường.</h1>
                    <p class="text-secondary">
                        Flow chính của bản Laravel mới là: admin tạo visit session, khách quét QR để nhận `visit_token`, sau đó hệ thống sẽ khởi tạo session chat và lưu `guest_uuid` trong session/cookie.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a class="btn btn-dark" href="{{ route('login') }}">Đăng nhập để chat</a>
                        <a class="btn btn-outline-dark" href="{{ route('about') }}">Xem mô tả hệ thống</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


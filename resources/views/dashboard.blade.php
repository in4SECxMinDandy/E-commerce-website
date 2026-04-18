{{--
    File frontend: dashboard.blade.php
    Chuc nang: Trang dashboard tuong thich cho nguoi dung da dang nhap, giu lai route /dashboard trong khi luong su dung chinh da chuyen ve /home.
    Vai tro giao dien: Hien thong bao dinh huong lai nguoi dung sang trang cong khai moi thay vi xay them dashboard phuc tap.
    Tuong tac: Chi hien noi dung tinh va mot lien ket dieu huong sang trang home.
--}}
@extends('layouts.base')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h1 class="h3 fw-bold mb-2">Dashboard</h1>
            <p class="text-secondary mb-0">Route dashboard được giữ lại để tương thích, nhưng flow chính đã chuyển sang <a href="{{ route('home') }}">/home</a>.</p>
        </div>
    </div>
@endsection


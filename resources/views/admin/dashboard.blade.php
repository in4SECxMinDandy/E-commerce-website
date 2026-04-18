{{--
    File frontend: admin/dashboard.blade.php
    Chuc nang: Man hinh tong quan nhanh cho khu vuc quan tri, hien cac thong so thong ke duoc backend truyen sang.
    Vai tro giao dien: Tao diem vao cho admin de nam so luong mon an, don hang, phien chat, phien QR hoac cac KPI dang co.
    Tuong tac: Giao dien chi render danh sach the thong ke, khong co form, phu thuoc vao du lieu mang $stats.
--}}
@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Dashboard quản trị</h1>
            <p class="text-secondary mb-0">Tổng quan nhanh cho phase foundation của bản clone Laravel.</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach ($stats as $label => $value)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="small text-secondary text-uppercase">{{ $label }}</div>
                        <div class="display-6 fw-bold mt-2">{{ $value }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection


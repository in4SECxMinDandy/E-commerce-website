{{--
    File frontend: components/auth-session-status.blade.php
    Chuc nang: Hien thong bao trang thai phien xac thuc, vi du gui thanh cong email reset hoac verify.
    Vai tro giao dien: Dong goi cach hien message de tai su dung trong cac man hinh auth.
    Tuong tac: Chi render khi bien $status co gia tri.
--}}
@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif


{{--
    File frontend: components/danger-button.blade.php
    Chuc nang: Nut hanh dong nguy hiem trong bo component Breeze.
    Vai tro giao dien: Dung cho cac thao tac can nhan manh muc do rui ro nhu xoa tai khoan, xoa du lieu.
    Tuong tac: Khong co logic rieng; nhan attributes va noi dung tu noi goi.
--}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>


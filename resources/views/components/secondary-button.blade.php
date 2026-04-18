{{--
    File frontend: components/secondary-button.blade.php
    Chuc nang: Nut phu trong bo component Breeze.
    Vai tro giao dien: Dung cho cac hanh dong thu cap nhu huy, dong modal hoac thao tac bo tro.
    Tuong tac: Khong chua logic rieng; nhan attributes va noi dung tu noi goi.
--}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>


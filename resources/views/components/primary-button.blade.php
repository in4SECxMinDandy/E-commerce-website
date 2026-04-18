{{--
    File frontend: components/primary-button.blade.php
    Chuc nang: Nut chinh trong he component Breeze.
    Vai tro giao dien: Dung cho hanh dong chinh cua form nhu luu, gui, xac nhan.
    Tuong tac: La wrapper style cho the button, cho phep noi goi truyen them attributes.
--}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>


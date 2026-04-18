{{--
    File frontend: components/dropdown-link.blade.php
    Chuc nang: Phan tu lien ket ben trong menu dropdown.
    Vai tro giao dien: Chuan hoa style cho tung muc trong menu tai khoan hoac menu tac vu cua Breeze.
    Tuong tac: Render the <a> voi class dung chung; khong chua logic dieu khien dropdown.
--}}
<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out']) }}>{{ $slot }}</a>


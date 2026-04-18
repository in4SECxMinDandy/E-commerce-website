{{--
    File frontend: components/text-input.blade.php
    Chuc nang: Component input co san style dung chung.
    Vai tro giao dien: Dong bo hinh thuc cho cac truong nhap text/password/email trong Breeze.
    Tuong tac: Ho tro disabled qua prop va merge them class/attribute tu ben ngoai.
--}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>


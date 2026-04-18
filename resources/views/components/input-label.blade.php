{{--
    File frontend: components/input-label.blade.php
    Chuc nang: Component label dung chung cho cac truong form.
    Vai tro giao dien: Chuan hoa typography va cach gan nhan cho input trong cac form Breeze.
    Tuong tac: Nhan gia tri qua prop value hoac slot de hien thi noi dung label.
--}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>


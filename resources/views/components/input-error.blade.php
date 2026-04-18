{{--
    File frontend: components/input-error.blade.php
    Chuc nang: Hien danh sach loi validate cho mot truong nhap.
    Vai tro giao dien: Giup thong bao loi nhap lieu gan sat truong input trong scaffold Breeze.
    Tuong tac: Chi render khi mang $messages co noi dung; lap qua tung loi de hien thi.
--}}
@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif


{{--
    File frontend: orders/history.blade.php
    Chuc nang: Trang lich su don hang cua nguoi dung.
    Vai tro giao dien: Hien bang tong hop cac don da tao cung bo loc theo trang thai va phan trang.
    Tuong tac: Form GET loc danh sach; bang render trang thai, tong tien va thoi diem dat mon de nguoi dung tu theo doi.
--}}
@extends('layouts.base')

@section('content')
    <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Lịch sử đơn hàng</h1>
            <p class="text-secondary mb-0">Theo dõi toàn bộ order đã tạo trong hệ thống Laravel mới.</p>
        </div>

        <form method="GET" class="d-flex gap-2">
            <select class="form-select" name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach (\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-dark" type="submit">Lọc</button>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Món</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Đặt lúc</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->food?->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format((float) $order->total_price, 0, ',', '.') }} đ</td>
                            <td><span class="badge text-bg-secondary">{{ $order->status->label() }}</span></td>
                            <td>{{ optional($order->ordered_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-4">Chưa có đơn hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
@endsection


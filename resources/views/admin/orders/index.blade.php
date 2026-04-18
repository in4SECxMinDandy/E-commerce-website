{{--
    File frontend: admin/orders/index.blade.php
    Chuc nang: Bang quan ly don hang cho admin kem bo loc theo trang thai.
    Vai tro giao dien: Hien thong tin khach, mon, so luong, tong tien va cho phep doi trang thai don ngay tren tung dong.
    Tuong tac: Form GET loc danh sach; moi dong co form PUT rieng de cap nhat trang thai don hang ma khong can vao trang sua rieng.
--}}
@extends('layouts.admin')

@section('content')
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                <div>
                    <h1 class="h4 mb-1">Quản lý đơn hàng</h1>
                    <p class="text-secondary mb-0">Admin có thể theo dõi và cập nhật trạng thái của order.</p>
                </div>
                <form method="GET" class="d-flex gap-2">
                    <select class="form-select" name="status">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-dark" type="submit">Lọc</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Khách</th>
                            <th>Món</th>
                            <th>SL</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->user?->full_name ?: 'Khách vãng lai' }}</td>
                                <td>{{ $order->food?->name }}</td>
                                <td>{{ $order->quantity }}</td>
                                <td>{{ number_format((float) $order->total_price, 0, ',', '.') }} đ</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select class="form-select form-select-sm" name="status">
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->value }}" @selected($order->status === $status)>{{ $status->label() }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-dark" type="submit">Lưu</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
@endsection

@push('scripts')
<script type="module">
    document.addEventListener('DOMContentLoaded', () => {
        if (window.Echo) {
            window.Echo.private('admin.orders')
                .listen('OrderCreated', (e) => {
                    const order = e.order;
                    // Play a notification sound or logic here if desired
                    
                    // Fetch the updated table seamlessly without F5
                    fetch(window.location.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(r => r.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Cập nhật lại table và phân trang
                        const newTable = doc.querySelector('.table-responsive');
                        const oldTable = document.querySelector('.table-responsive');
                        if (newTable && oldTable) {
                            oldTable.innerHTML = newTable.innerHTML;
                        }
                        
                        // Hiển thị thông báo Toast góc trên bên phải (nếu có thư viện hoặc dùng Bootstrap)
                        if (typeof bootstrap !== 'undefined') {
                            const toastContainer = document.createElement('div');
                            toastContainer.className = 'position-fixed top-0 end-0 p-3';
                            toastContainer.style.zIndex = 9999;
                            toastContainer.innerHTML = `
                                <div class="toast show align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                  <div class="d-flex">
                                    <div class="toast-body">
                                      🔔 <strong>Đơn hàng mới!</strong> Khách hàng vừa đặt đơn #${order.id}.
                                    </div>
                                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                  </div>
                                </div>
                            `;
                            document.body.appendChild(toastContainer);
                            setTimeout(() => {
                                toastContainer.remove();
                            }, 5000);
                        }
                    })
                    .catch(error => console.error("Could not fetch updated orders:", error));
                });
        }
    });
</script>
@endpush


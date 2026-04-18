{{--
File frontend: foods/show.blade.php
Chuc nang: Trang chi tiet mon an va khu dat mon.
Vai tro giao dien: Trinh bay thong tin day du cua mon, gia, ton kho, trang thai featured va danh sach mon lien quan.
Tuong tac: Neu da dang nhap thi hien form dat mon qua AJAX; neu chua dang nhap thi hien thong bao va link login.
Dua tren everything-claude-code skills: api-design, laravel-patterns, laravel-security
--}}
@extends('layouts.base')

@auth
    @push('scripts')
        <script type="module">
            import { submitOrder, showOrderSuccess, showOrderError } from '{{ Vite::asset('resources/js/modules/ajax-orders.js') }}';

            document.addEventListener('DOMContentLoaded', () => {
                const form = document.querySelector('[data-order-form]');
                const submitBtn = form?.querySelector('[type="submit"]');
                const quantityInput = form?.querySelector('[name="quantity"]');
                const noteInput = form?.querySelector('[name="note"]');
                const foodId = form?.querySelector('[name="food_id"]')?.value;

                if (!form || !foodId) return;

                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Đang xử lý...';
                    }

                    try {
                        const data = {
                            food_id: parseInt(foodId, 10),
                            quantity: parseInt(quantityInput?.value || '1', 10),
                            note: noteInput?.value || '',
                        };

                        const response = await submitOrder(data);

                        if (response.success) {
                            showOrderSuccess(response.message, response.data);
                            form.reset();
                            quantityInput.value = '1';
                        }
                    } catch (error) {
                        showOrderError(error);
                    } finally {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Đặt ngay';
                        }
                    }
                });
            });
        </script>
    @endpush
@endauth

@section('content')
    @php($canOrder = $food->is_available && $food->stock > 0)

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 p-lg-5">
                    <div class="small text-secondary mb-2">{{ $food->category?->name }}</div>
                    <h1 class="display-6 fw-bold">{{ $food->name }}</h1>
                    <p class="lead text-secondary">
                        {{ $food->short_description ?: 'Món này đang được cập nhật thông tin chi tiết trong bản Laravel mới.' }}
                    </p>
                    <div class="my-4 fs-2 fw-bold">{{ number_format((float) $food->price, 0, ',', '.') }} đ</div>
                    <p>{{ $food->description ?: 'Chưa có mô tả chi tiết.' }}</p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <span class="badge {{ $canOrder ? 'text-bg-success' : 'text-bg-danger' }}">Tồn kho: {{ $food->stock }}</span>
                        @if ($food->is_featured)
                            <span class="badge text-bg-dark">Featured</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 mb-3">Đặt món</h2>

                    @auth
                        @if ($canOrder)
                            <form class="vstack gap-3" method="POST" action="{{ route('orders.store') }}" data-order-form>
                                @csrf
                                <input type="hidden" name="food_id" value="{{ $food->id }}">

                                <div>
                                    <label for="quantity" class="form-label">Số lượng</label>
                                    <input
                                        type="number"
                                        min="1"
                                        max="{{ $food->stock }}"
                                        class="form-control"
                                        id="quantity"
                                        name="quantity"
                                        value="{{ min((int) old('quantity', 1), $food->stock) }}"
                                        required
                                    >
                                    <div class="form-text">Bạn có thể đặt tối đa {{ $food->stock }} sản phẩm.</div>
                                </div>

                                <div>
                                    <label for="note" class="form-label">Ghi chú</label>
                                    <textarea class="form-control" id="note" name="note" rows="3">{{ old('note') }}</textarea>
                                </div>

                                <button class="btn btn-dark" type="submit">Đặt ngay</button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0">
                                Món này hiện đã hết hàng hoặc tạm ngưng bán.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-secondary mb-0">
                            Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để đặt món và xem lịch sử đơn hàng.
                        </div>
                    @endauth
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Món liên quan</h2>
                    <div class="vstack gap-3">
                        @forelse ($relatedFoods as $relatedFood)
                            <a class="text-decoration-none text-dark border rounded-4 p-3"
                                href="{{ route('foods.show', $relatedFood) }}">
                                <div class="fw-semibold">{{ $relatedFood->name }}</div>
                                <div class="small text-secondary">{{ number_format((float) $relatedFood->price, 0, ',', '.') }}
                                    đ</div>
                            </a>
                        @empty
                            <div class="text-secondary">Chưa có món liên quan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

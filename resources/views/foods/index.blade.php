{{--
File frontend: foods/index.blade.php
Chuc nang: Trang danh sach mon an cong khai.
Vai tro giao dien: Hien catalog voi AJAX filter, search va phan trang.
Tuong tac: Form loc gui AJAX request, danh sach cap nhat dong mà không reload trang.
Dựa trên everything-claude-code skills: api-design, laravel-patterns
--}}
@extends('layouts.base')

@push('scripts')
    <script type="module">
        import { fetchFoods, renderFoodCards, renderPagination } from '{{ Vite::asset('resources/js/modules/ajax-foods.js') }}';

        document.addEventListener('DOMContentLoaded', () => {
            const foodsContainer = document.querySelector('[data-foods-container]');
            const paginationContainer = document.querySelector('[data-foods-pagination]');
            const filterForm = document.querySelector('[data-food-filter-form]');

            if (!foodsContainer) return;

            let currentParams = {
                category: filterForm?.querySelector('[name="category"]')?.value || '',
                q: filterForm?.querySelector('[name="q"]')?.value || '',
                page: 1,
            };

            /**
             * Load foods via AJAX và update UI
             */
            async function loadFoods(params = {}) {
                currentParams = { ...currentParams, ...params };

                // Show loading state
                foodsContainer.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-dark" role="status"></div></div>';

                try {
                    const response = await fetchFoods(currentParams);

                    // Update foods grid
                    foodsContainer.innerHTML = renderFoodCards(response.data);

                    // Update pagination
                    if (paginationContainer) {
                        paginationContainer.innerHTML = renderPagination(response.meta);
                    }

                    // Update URL without reload
                    const url = new URL(window.location);
                    if (currentParams.category) url.searchParams.set('category', currentParams.category);
                    else url.searchParams.delete('category');
                    if (currentParams.q) url.searchParams.set('q', currentParams.q);
                    else url.searchParams.delete('q');
                    if (currentParams.page > 1) url.searchParams.set('page', currentParams.page);
                    else url.searchParams.delete('page');
                    window.history.replaceState({}, '', url);
                } catch (error) {
                    console.error('Load foods failed:', error);
                    foodsContainer.innerHTML = '<div class="col-12"><div class="alert alert-danger">Không thể tải danh sách món. Vui lòng thử lại.</div></div>';
                }
            }

            /**
             * Handle filter form submission
             */
            if (filterForm) {
                filterForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    currentParams.page = 1; // Reset to page 1 on new filter
                    loadFoods({
                        category: filterForm.querySelector('[name="category"]')?.value || '',
                        q: filterForm.querySelector('[name="q"]')?.value || '',
                    });
                });
            }

            /**
             * Handle pagination clicks
             */
            document.addEventListener('click', (e) => {
                const pageLink = e.target.closest('[data-page]');
                if (pageLink) {
                    e.preventDefault();
                    const page = parseInt(pageLink.dataset.page, 10);
                    if (!isNaN(page) && page > 0) {
                        loadFoods({ page });
                        // Scroll to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            });

            // Initial load (will skip if no params)
            if (currentParams.category || currentParams.q) {
                loadFoods();
            }
        });
    </script>
@endpush

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <h1 class="display-6 fw-bold mb-1">Danh sách thực phẩm</h1>
            <p class="text-secondary mb-0">Danh sách món với bộ lọc AJAX - không reload trang.</p>
        </div>

        <form class="row g-2 align-items-end" data-food-filter-form>
            <div class="col-sm-auto">
                <label for="category" class="form-label mb-1">Danh mục</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Tất cả</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected($activeCategory === $category->slug)>{{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-auto">
                <label for="q" class="form-label mb-1">Tìm kiếm</label>
                <input class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Trà sữa, topping...">
            </div>
            <div class="col-sm-auto">
                <button class="btn btn-dark w-100" type="submit">Lọc</button>
            </div>
        </form>
    </div>

    <div class="row g-4" data-foods-container>
        @forelse ($foods as $food)
            <div class="col-md-6 col-xl-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="small text-secondary">{{ $food->category?->name }}</div>
                        <h2 class="h4 mt-1">{{ $food->name }}</h2>
                        <p class="text-secondary">{{ $food->short_description ?: $food->description ?: 'Đang cập nhật mô tả.' }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <div class="fw-bold fs-5">{{ number_format((float) $food->price, 0, ',', '.') }} đ</div>
                                <div class="small {{ $food->stock > 0 ? 'text-success' : 'text-danger' }}">Tồn kho:
                                    {{ $food->stock }}</div>
                            </div>
                            <a href="{{ route('foods.show', $food) }}" class="btn btn-outline-dark">Xem món</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary mb-0">Không tìm thấy món phù hợp.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4" data-foods-pagination>
        {{ $foods->links() }}
    </div>
@endsection
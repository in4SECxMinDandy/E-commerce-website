{{--
    File frontend: pages/home.blade.php
    Chuc nang: Trang chu cong khai cua he thong.
    Vai tro giao dien: Gom hero gioi thieu du an, danh sach danh muc noi bat va cac mon featured de dan luong nguoi dung vao catalog/chat.
    Tuong tac: Nhieu nut CTA dieu huong den danh sach mon va trang chat; du lieu danh muc va featured foods duoc render tu backend.
--}}
@extends('layouts.base')

@section('content')
    <section class="hero-card p-4 p-lg-5 rounded-4 mb-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="badge text-bg-light mb-3">Nền tảng vận hành Universal Tea</span>
                <h1 class="display-5 fw-bold mb-3">Universal Tea mang đến trải nghiệm giới thiệu sản phẩm, đặt món và hỗ trợ khách hàng trên một hệ thống thống nhất, rõ ràng và dễ vận hành.</h1>
                <p class="lead text-secondary mb-4">
                    Dự án được phát triển trên Laravel, Blade và Bootstrap 5.3 nhằm xây dựng một nền tảng PHP-native ổn định, sẵn sàng mở rộng cho danh mục thực phẩm, quy trình đặt hàng, lịch sử giao dịch và kênh chat hỗ trợ trực tiếp.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-dark btn-lg" href="{{ route('foods.index') }}">Khám phá thực phẩm</a>
                    <a class="btn btn-outline-dark btn-lg" href="{{ route('chat.show') }}">Liên hệ tư vấn</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="small text-secondary text-uppercase">Danh mục tiêu biểu</div>
                                <div class="fw-semibold">Nhóm sản phẩm nổi bật</div>
                            </div>
                            <span class="badge text-bg-dark">{{ $categories->count() }} mục</span>
                        </div>
                        <div class="row g-3">
                            @foreach ($categories->take(4) as $category)
                                <div class="col-6">
                                    <div class="rounded-4 bg-body-tertiary p-3 h-100">
                                        <div class="small text-secondary text-uppercase">Danh mục</div>
                                        <div class="fw-semibold">{{ $category->name }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h3 mb-1">Sản phẩm nổi bật</h2>
                <p class="text-secondary mb-0">Những lựa chọn đang được ưu tiên giới thiệu tới khách hàng trên hệ thống.</p>
            </div>
            <a href="{{ route('foods.index') }}" class="btn btn-outline-dark">Xem tất cả</a>
        </div>

        <div class="row g-4">
            @forelse ($featuredFoods as $food)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <div>
                                    <div class="small text-secondary">{{ $food->category?->name }}</div>
                                    <h3 class="h5 mb-1">{{ $food->name }}</h3>
                                </div>
                                <span class="badge text-bg-success">Còn {{ $food->stock }}</span>
                            </div>
                            <p class="text-secondary">{{ $food->short_description ?: $food->description ?: 'Thông tin mô tả sản phẩm đang được cập nhật.' }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <span class="fs-5 fw-bold">{{ number_format((float) $food->price, 0, ',', '.') }} đ</span>
                                <a href="{{ route('foods.show', $food) }}" class="btn btn-dark">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-secondary mb-0">Hiện chưa có sản phẩm nổi bật. Vui lòng cập nhật dữ liệu từ khu vực quản trị.</div>
                </div>
            @endforelse
        </div>
    </section>
@endsection

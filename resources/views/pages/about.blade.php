{{--
    File frontend: pages/about.blade.php
    Chuc nang: Trang gioi thieu he thong va dinh huong ky thuat.
    Vai tro giao dien: Trinh bay muc tieu cutover, stack cong nghe va cach du an tiep can viec clone chuc nang tu he thong cu.
    Tuong tac: Chu yeu la noi dung thong tin tinh, giup nguoi doc hieu boi canh san pham.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-dark mb-3">Giới thiệu dự án</span>
                    <h1 class="display-6 fw-bold mb-3">Universal Tea là nền tảng được phát triển để chuẩn hóa trải nghiệm giới thiệu sản phẩm, xử lý đơn hàng và hỗ trợ khách hàng trong một hệ sinh thái vận hành tập trung.</h1>
                    <p class="text-secondary mb-3">
                        Dự án hướng đến một kiến trúc PHP-native hiện đại, giúp doanh nghiệp quản lý danh mục thực phẩm, theo dõi đơn hàng, kiểm soát vận hành nội bộ và duy trì tương tác với khách hàng theo thời gian thực trên cùng một hệ thống.
                    </p>
                    <p class="text-secondary mb-0">
                        Thay vì chỉ tái tạo giao diện, phiên bản này tập trung vào tính ổn định, khả năng mở rộng và sự nhất quán trong luồng nghiệp vụ, tạo nền tảng phù hợp cho giai đoạn triển khai thực tế và nâng cấp lâu dài.
                    </p>

                    <div class="row g-3 mt-4">
                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-2">Định hướng sản phẩm</div>
                                <p class="mb-0 text-secondary">Xây dựng trải nghiệm liền mạch từ khám phá thực phẩm, đặt món, theo dõi lịch sử đến hỗ trợ khách hàng trực tuyến.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-2">Nền tảng công nghệ</div>
                                <p class="mb-0 text-secondary">Laravel 11, Blade, Bootstrap 5.3, PostgreSQL, Redis và Reverb, ưu tiên hiệu năng ổn định và khả năng mở rộng rõ ràng.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-4 p-4 h-100 bg-body-tertiary">
                                <div class="fw-semibold mb-2">Mục tiêu vận hành</div>
                                <p class="mb-0 text-secondary">Giữ nhất quán các URL và hành vi nghiệp vụ quan trọng để hỗ trợ quá trình chuyển đổi hệ thống an toàn, mạch lạc và dễ kiểm soát.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

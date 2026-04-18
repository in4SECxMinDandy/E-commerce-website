# Universal Tea - E-commerce Website 🍵

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-5.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Reverb](https://img.shields.io/badge/Reverb-Websockets-FF2D20?style=for-the-badge)

Dự án E-commerce chuyên nghiệp xây dựng bằng **Laravel 11**, **Blade**, và **Bootstrap 5.3**, tích hợp hệ thống **Realtime** (thời gian thực) bằng Laravel Reverb. 

Dự án mô phỏng một cửa hàng trà sữa/thức uống trực tuyến (Universal Tea) với đầy đủ các tính năng dành cho khách hàng và bộ phân quản trị.

---

## 🚀 Tính năng nổi bật (Key Features)

### 🛍️ Dành cho Khách hàng (Customer)
*   **Danh mục & Thực đơn**: Xem danh sách món ăn, chi tiết món ăn.
*   **Xác thực người dùng**: Đăng nhập, đăng ký nhanh chóng bằng `Laravel Breeze`.
*   **Đặt hàng mượt mà**: Nút "Đặt ngay" xử lý ngầm (AJAX) đi kèm trải nghiệm người dùng ấn tượng với cửa sổ thông báo **SweetAlert2**.
*   **Lịch sử đơn hàng**: Khách hàng quản lý chi tiết các giao dịch mua thông minh.
*   **Tương tác trực tiếp**: Hỗ trợ Live Chat (Guest Chat / Auth Chat) vận hành realtime.

### 🛡️ Dành cho Quản trị viên (Admin)
*   **Dashboard Hiện Đại**: Theo dõi và quản lý dữ liệu tập trung.
*   **Quản Lý Realtime**: Hệ thống Đơn hàng tại `/admin/orders` tự động nhận bản cập nhật tức thời khi có khách đặt hàng (Không cần tải lại trang / F5). Giải pháp tích hợp bằng Laravel Echo, Reverb và DOM Fetch.
*   **Quản lý danh mục & Món ăn**: Thêm, sửa, xóa, hình ảnh thu nhỏ, hỗ trợ quản lý tình trạng tồn kho (Stock) chi tiết.
*   **Phân quyền nghiêm ngặt**: 100% tài khoản cách ly quyền hạn bằng `Spatie Laravel Permission`.
*   **Cổng đăng nhập biệt lập**: `/adminlogin` được tách rời để nâng cao tính bảo mật.
*   **Hỗ trợ Chat**: Admin có thể quản lí, trả lời và đóng phiên chat của khách hàng một cách khoa học.
*   **Phiên truy cập (Visit Sessions)**: Quản lý token, sẵn sàn phục vụ thiết kế quét QR đa nền tảng.

---

## 🛠 Công nghệ xây dựng (Tech Stack)

*   **Backend**: PHP 8.2+ / Laravel 11.
*   **Realtime**: Laravel Reverb, Predis.
*   **Database**: SQLite (cho Local) và PostgreSQL + Redis (Mục tiêu Production).
*   **Quyền hạn**: Laravel Breeze, Spatie Permission.
*   **Frontend**: Blade template, Bootstrap 5.3, Sass & Vite Bundle.
*   **Tương tác JS / Websockets**: Axios, SweetAlert2, Laravel Echo, Pusher JS.

---

## ⚙️ Hướng dẫn cài đặt & Chạy Local

Dự án sử dụng cơ sở dữ liệu `SQLite` nhằm giúp các lập trình viên cài đặt và trải nghiệm nhanh nhất.

### Yêu cầu tiên quyết
*   **PHP** >= 8.2
*   **Composer**
*   **Node.js** >= 20.x & **npm**

### Các bước khởi tạo

**1. Clone kho lưu trữ Repository:**
```bash
git clone https://github.com/in4SECxMinDandy/E-commerce-website.git
cd E-commerce-website
```

**2. Cài đặt các thư viện lõi:**
```bash
composer install
npm install
```

**3. Thiết lập file cấu hình môi trường (.env):**
```bash
# Lệnh copy file
cp .env.example .env

# Nếu là Windows (powershell) dùng:
# Copy-Item .env.example .env
```
Mặc định dùng SQLite chạy cục bộ, nên file `.env` cần đảm bảo các thông số sau:
```env
DB_CONNECTION=sqlite
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=reverb
```

**4. Khởi tạo Cơ sở Dữ Liệu và Filesystem:**
```bash
# Cấp khóa ứng dụng
php artisan key:generate

# Chạy migration và seed dữ liệu mẫu
php artisan migrate:fresh --seed

# Liên kết thư mục chứa ảnh
php artisan storage:link
```

**5. Build tài nguyên Frontend JS/CSS:**
```bash
npm run build
```

**6. Khởi động Ứng dụng (Yêu cầu bật 3 Terminal để vận hành đầy đủ Realtime):**
```bash
# Terminal 1: Chạy Server Laravel (Truy cập Web)
php artisan serve

# Terminal 2: Chạy Server Reverb (Nhận thông báo Realtime / Websockets)
php artisan reverb:start

# Terminal 3: Chạy Queue worker cho hệ thống Broadcast chạy ngầm
php artisan queue:work
```

👉 Hãy mở trình duyệt và truy cập: `http://127.0.0.1:8000`

---

## 🔑 Tài khoản Mặc định (Theo Seeders)

Sau khi chạy lệnh `php artisan migrate:fresh --seed`, hệ thống tạo ra 2 vị trí role sẵn sàng dùng nghiệm thu tính năng.

✅ **Quản trị viên (Admin)**
- **URL truy cập:** `http://127.0.0.1:8000/adminlogin`
- **Email:** `admin@universaltea.test`
- **Mật khẩu:** `password`

✅ **Khách hàng (Customer)**
- **URL truy cập:** `http://127.0.0.1:8000/login`
- **Email:** `test@example.com`
- **Mật khẩu:** `password`

---

## 📂 Cơ sở tài liệu (Documentation)

Toàn bộ các tài liệu kỹ thuật, kế hoạch thiết kế sơ khai, hoặc Plan Cấu trúc đều được lưu lại tham khảo.
Xem định tuyến và thông tin thay đổi qua: [docs/php-bootstrap-clone-plan.md](docs/php-bootstrap-clone-plan.md).

---

## 🧪 Kiểm thử (Testing)

Dự án luôn đề cao Code Quality. Sử dụng `Pest` và `PHPUnit` để tự động hóa quá trình chạy test bảo vệ module Đơn hàng, Middleware bảo mật:

```bash
vendor/bin/pest
# Hoặc 
php artisan test
```

---

## 🗺️ Nhóm cải tiến tương lai (Roadmap)
*   [x] Hoàn thiện tích hợp SweetAlert2, fix giao diện UI trải nghiệm cho Ajax Orders.
*   [x] Chuyển quản lý /admin/orders thành tự động Reload Server Table mà không cần F5.
*   [ ] Bổ sung hệ Generator mã QR dùng trong hệ thống khách vãng lai.
*   [ ] Chuẩn hoá cho quy trình Deploy qua PostgreSQL.
*   [ ] Viết Feature Test sâu hơn cho Websockets.
# Universal Tea PHP Clone

## Tổng quan

Đây là bản dựng lại dự án theo kiến trúc **Laravel + Blade + Bootstrap 5.3** với mục tiêu tiến tới thay thế runtime cũ bằng một ứng dụng PHP-native. Dự án hiện đã có nền tảng hoạt động được cho các nhóm tính năng chính: public pages, xác thực, quản trị, danh mục món, đơn hàng, lịch sử đơn hàng, guest chat, admin chat, visit session và khung realtime qua Reverb/Echo.

Tài liệu kế hoạch chi tiết nằm tại [docs/php-bootstrap-clone-plan.md](C:\Users\haqua\OneDrive\Desktop\BigProject\Web\BT3 PHP\docs\php-bootstrap-clone-plan.md).

## Mục tiêu dự án

- Giữ **functional parity** với hệ thống cũ theo hướng Laravel-native.
- Giữ các URL công khai quan trọng để hạn chế thay đổi hành vi người dùng.
- Tách khỏi các phụ thuộc runtime của Supabase cho auth, storage và realtime.
- Chuẩn bị hạ tầng để cutover qua PostgreSQL + Redis + Reverb trong môi trường staging/production.

## Trạng thái hiện tại

Hiện tại repo đã có:

- Khung Laravel 11 chạy được với Blade + Bootstrap 5.3 + Vite.
- Auth người dùng bằng Laravel Breeze (Blade).
- Đăng nhập admin riêng qua `/adminlogin`, dùng chung backend session auth.
- Phân quyền admin bằng `spatie/laravel-permission`.
- Migration và model cho:
  - `users`
  - `food_categories`
  - `foods`
  - `orders`
  - `visit_sessions`
  - `chat_sessions`
  - `chat_messages`
  - bảng role/permission
- Public routes:
  - `/home`
  - `/gioi-thieu`
  - `/thuc-pham`
  - `/thuc-pham/{slug}`
  - `/chat`
  - `/history`
- Admin routes:
  - `/admin`
  - `/admin/foods`
  - `/admin/categories`
  - `/admin/orders`
  - `/admin/chat`
  - `/admin/visit-sessions`
- Async endpoints:
  - `POST /chat/send`
  - `POST /chat/upload-image`
  - `GET /chat/messages`
  - `POST /orders`
  - `POST /admin/login`
  - `POST /admin/chat/{session}/close`
  - `DELETE /admin/chat/{session}`
  - `DELETE /admin/chat/{session}/messages/{message}`
- Seed dữ liệu mẫu để có thể mở app và test flow ngay.
- Test auth/profile bằng Pest đang pass.

Phần **chưa hoàn thiện đầy đủ**:

- Import dữ liệu thật từ hệ cũ mới ở mức scaffold command.
- QR image generator chưa được dựng, hiện visit session tạo link token trước.
- Realtime với Reverb/Redis đã có wiring trong code nhưng cần cấu hình môi trường đúng để chạy đầy đủ ngoài local tối giản.
- Một số text và workflow vẫn là foundation scaffold, chưa phải bản parity cuối cùng.

## Công nghệ sử dụng

### Backend

- PHP 8.2+ hiện tại, mục tiêu production là PHP 8.3
- Laravel 11
- Laravel Reverb
- Predis
- Spatie Laravel Permission

### Frontend

- Blade
- Bootstrap 5.3
- Vite
- Sass
- Laravel Echo
- Pusher JS client cho Reverb protocol

### Test

- Pest
- PHPUnit

## Cấu trúc thư mục chính

- `app/`
  - Controller, model, enum, middleware, service layer
- `config/`
  - Cấu hình Laravel, permission, broadcasting và `universaltea.php`
- `database/migrations/`
  - Toàn bộ schema của ứng dụng mới
- `database/seeders/`
  - Seed account và dữ liệu mẫu
- `resources/views/`
  - Giao diện Blade cho public, auth, admin, chat, catalog
- `resources/scss/`
  - Theme Bootstrap và style tùy biến
- `resources/js/`
  - Bootstrap JS, Echo/Reverb wiring
- `routes/web.php`
  - Route công khai và admin
- `routes/console.php`
  - Command scaffold cho import và bootstrap admin
- `storage/logs/`
  - Log runtime của Laravel và log server local
- `docs/php-bootstrap-clone-plan.md`
  - Plan chuyển đổi chi tiết

## Yêu cầu môi trường

### Tối thiểu để chạy local nhanh

- PHP 8.2 trở lên
- Composer
- Node.js 20+ hoặc tương đương
- npm

### Mục tiêu staging/production

- PHP 8.3
- PostgreSQL
- Redis
- Reverb
- Queue worker

## Hai cách chạy local

### Cách 1: Chạy nhanh với SQLite local

Đây là cách dễ nhất để mở app ngay trên máy hiện tại.

1. Cài dependency PHP:

```powershell
composer install
```

2. Cài dependency frontend:

```powershell
npm install
```

3. Chuẩn bị file môi trường:

```powershell
Copy-Item .env.example .env
```

4. Nếu muốn chạy nhanh bằng SQLite local, chỉnh `.env` về:

```env
DB_CONNECTION=sqlite
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
REDIS_CLIENT=phpredis
```

Với cách này, có thể để file SQLite tại:

```text
database/database.sqlite
```

5. Tạo key ứng dụng:

```powershell
php artisan key:generate
```

6. Chạy migrate và seed:

```powershell
php artisan migrate:fresh --seed
```

7. Tạo symbolic link cho storage public:

```powershell
php artisan storage:link
```

8. Build frontend:

```powershell
npm run build
```

Hoặc nếu muốn chạy dev server Vite:

```powershell
npm run dev
```

9. Chạy ứng dụng:

```powershell
php artisan serve
```

Ứng dụng sẽ mở tại:

```text
http://127.0.0.1:8000
```

## Thông tin đăng nhập mặc định (Credentials)

Sau khi chạy lệnh `db:seed`, bạn có thể sử dụng các tài khoản sau:

### 🔐 Quản trị viên (Admin)
- **URL đăng nhập:** `http://127.0.0.1:8000/adminlogin`
- **Email:** `admin@universaltea.test`
- **Mật khẩu:** `password`

### 👤 Người dùng (Customer)
- **URL đăng nhập:** `http://127.0.0.1:8000/login`
- **Email:** `test@example.com`
- **Mật khẩu:** `password`

### Cách 2: Chạy theo cấu hình mục tiêu PostgreSQL + Redis + Reverb

File `.env.example` hiện mô tả cấu hình mục tiêu này:

- `DB_CONNECTION=pgsql`
- `SESSION_DRIVER=redis`
- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`
- `BROADCAST_CONNECTION=reverb`
- `FILESYSTEM_DISK=public`
- `REDIS_CLIENT=predis`

Quy trình:

1. Tạo database PostgreSQL.
2. Khởi động Redis.
3. Sao chép `.env.example` thành `.env`.
4. Chỉnh thông số kết nối cho phù hợp máy của bạn.
5. Chạy:

```powershell
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan reverb:start
php artisan queue:listen
php artisan serve
```

Lưu ý:

- Realtime chat sẽ chỉ hoạt động đúng khi `BROADCAST_CONNECTION=reverb` và Reverb server đang chạy.
- Nếu websocket không chạy, phần giao diện chat hiện có polling fallback trong frontend.


## Các lệnh hữu ích

### Chạy test

```powershell
vendor\bin\pest
```

### Build frontend production

```powershell
npm run build
```

### Chạy dev frontend

```powershell
npm run dev
```

### Xem danh sách route

```powershell
php artisan route:list
```

### Tạo hoặc cập nhật tài khoản admin

```powershell
php artisan universaltea:bootstrap-admin
```

Hoặc chỉ định email/mật khẩu:

```powershell
php artisan universaltea:bootstrap-admin admin@domain.test secret123
```

### Chạy scaffold import dữ liệu

```powershell
php artisan import:universaltea --dry-run
```

Lệnh này hiện mới là khung quy trình import, chưa có logic migrate dữ liệu thật.

## Tài nguyên public và route chính

### Public

- `/` → redirect sang `/home`
- `/home`
- `/gioi-thieu`
- `/thuc-pham`
- `/thuc-pham/{slug}`
- `/login`
- `/register`
- `/chat`
- `/history` yêu cầu đăng nhập
- `/profile` yêu cầu đăng nhập

### Admin

- `/adminlogin`
- `/admin`
- `/admin/foods`
- `/admin/categories`
- `/admin/orders`
- `/admin/chat`
- `/admin/visit-sessions`

### API/form async

- `POST /orders`
- `POST /chat/send`
- `POST /chat/upload-image`
- `GET /chat/messages`
- `POST /admin/login`
- `POST /admin/chat/{session}/reply`
- `POST /admin/chat/{session}/close`
- `DELETE /admin/chat/{session}`
- `DELETE /admin/chat/{session}/messages/{message}`

## Dữ liệu và seed mẫu

Seeder hiện tạo:

- 1 admin role
- 1 customer role
- 1 tài khoản admin
- 1 tài khoản user
- 1 danh mục mẫu: `Trà sữa`
- 1 món mẫu: `Trà sữa trân châu hoàng gia`

Điều này đủ để:

- mở `/home`
- xem catalog ở `/thuc-pham`
- đăng nhập user/admin
- thử flow order và admin CRUD cơ bản

## Realtime chat

Code hiện đã có:

- broadcasting config
- `routes/channels.php`
- event:
  - `ChatMessageCreated`
  - `ChatSessionStatusChanged`
- frontend Echo client trong `resources/js/bootstrap.js` và `resources/js/echo.js`

Để dùng realtime thật:

1. Bật `BROADCAST_CONNECTION=reverb`
2. Bật Redis
3. Chạy:

```powershell
php artisan reverb:start
```

4. Build hoặc chạy frontend dev server

Nếu websocket lỗi, frontend vẫn có polling fallback cho phần chat.

## Log và kiểm tra lỗi

### Log ứng dụng Laravel

File log chính:

```text
storage/logs/laravel.log
```

### Log khi tự chạy `php artisan serve`

Nếu bạn tự redirect output hoặc chạy app ở nền, có thể lưu log tại:

```text
storage/logs/php-serve-*.out.log
storage/logs/php-serve-*.err.log
```

### Một số lệnh kiểm tra nhanh

```powershell
php artisan about
php artisan route:list
vendor\bin\pest
Get-Content storage\logs\laravel.log -Tail 100
```

## Kiểm thử đã xác nhận

Tại thời điểm cập nhật README này:

- `vendor\bin\pest` đang pass
- `npm run build` build thành công
- app local đã được chạy và kiểm tra các route chính:
  - `/up`
  - `/home`
  - `/thuc-pham`
  - `/login`
  - `/adminlogin`
  - `/chat`

## Lưu ý quan trọng

- `README.md` này phản ánh **trạng thái code hiện tại**, không chỉ trạng thái mục tiêu.
- `.env.example` phản ánh **cấu hình mục tiêu staging/production**.
- `.env` local có thể khác để phục vụ chạy nhanh bằng SQLite.
- Mục tiêu cuối cùng vẫn là stack:
  - Laravel 11
  - Blade
  - Bootstrap 5.3
  - PostgreSQL
  - Redis
  - Reverb

## Roadmap gần

Các bước nên làm tiếp theo:

1. Hoàn thiện import pipeline thật cho `import:universaltea`.
2. Dựng QR generator thay vì chỉ tạo visit token/link.
3. Bổ sung feature test cho order, admin CRUD, visit session và chat.
4. Chuyển hoàn toàn local/staging sang PostgreSQL + Redis.
5. Kiểm tra đầy đủ realtime và polling fallback.
6. Hoàn thiện các màn còn ở mức foundation scaffold.

## Tài liệu liên quan

- Kế hoạch clone chi tiết: [docs/php-bootstrap-clone-plan.md](C:\Users\haqua\OneDrive\Desktop\BigProject\Web\BT3 PHP\docs\php-bootstrap-clone-plan.md)

#   E - c o m m e r c e - w e b s i t e  
 
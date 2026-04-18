# `docs/php-bootstrap-clone-plan.md`

## Tóm tắt
- Xây lại dự án theo kiến trúc **Laravel + Blade + Bootstrap 5.3** trên **PHP 8.3**, và bằng hạ tầng PHP-native.
- Mục tiêu là **full parity theo chức năng**, không phải pixel-perfect Tailwind clone: vẫn có catalog, order, auth, admin dashboard, QR visit session, guest chat, admin chat, history, upload ảnh, bảo mật, rate limit, realtime chat và polling fallback.
- Bản PHP sẽ là **một ứng dụng song song mới**, không chuyển đổi từng file từ Next.js; cutover sẽ theo hướng import dữ liệu và đổi traffic sau khi staging pass.

## Kiến trúc đích và interface công khai
- Frontend dùng **Blade + Bootstrap 5.3 + Vite**. Dùng **vanilla JS** cho form/filter/modal; chỉ dùng JS bổ sung cho chat realtime.
- Giữ URL chính để giảm thay đổi hành vi người dùng: `/home`, `/thuc-pham`, `/thuc-pham/{slug}`, `/gioi-thieu`, `/login`, `/chat`, `/history`, `/admin`, `/admin/foods`, `/admin/categories`, `/admin/orders`, `/admin/chat`, `/adminlogin`.
- Auth dùng **Laravel Breeze (Blade)** cho user/admin; phân quyền dùng **spatie/laravel-permission**. `/login` và `/adminlogin` là hai view khác nhau nhưng đi cùng một backend auth.
- Guest QR chat không dùng Supabase anonymous auth nữa; thay bằng **`visit_token` + `guest_uuid` lưu trong session/cookie**. `chat_sessions` phải hỗ trợ cả người dùng đăng nhập và guest.
- Storage dùng **Laravel Filesystem `public` disk** ngay từ v1. Quy ước:
  - `food-images/{uuid}.{ext}`
  - `chat-images/{actor}/{uuid}.{ext}`
- Realtime chat dùng **Laravel Reverb + Redis + queue worker**. Fallback là polling 30 giây khi websocket lỗi hoặc user inactive.
- Các endpoint async cần có:
  - `POST /chat/send`
  - `POST /chat/upload-image`
  - `GET /chat/messages?session_id=...&cursor=...`
  - `POST /orders`
  - `POST /admin/login`
  - `POST /admin/chat/{session}/close`
  - `DELETE /admin/chat/{session}`
  - `DELETE /admin/chat/{session}/messages/{message}` nếu giữ chức năng xóa tin riêng
- Bảo mật server-side phải giữ tương đương bản hiện tại: request validation, rate limiting, file validation bằng magic bytes, role check, ownership check, CSRF, session auth, middleware bảo vệ admin/history/chat.

## Mô hình dữ liệu và chiến lược migrate
- Không giữ phụ thuộc `auth.users` của Supabase. Dùng schema Laravel-native trên PostgreSQL.
- Bảng đích:
  - `users`: email, password, full_name, is_active, timestamps
  - role tables của `spatie/laravel-permission`
  - `food_categories`
  - `foods`
  - `orders`
  - `visit_sessions`
  - `chat_sessions`
  - `chat_messages`
- Thiết kế chat đích:
  - `chat_sessions`: `id`, `user_id` nullable, `guest_uuid` nullable, `guest_name`, `visit_session_id`, `visit_token`, `status`, `session_type`, `opened_at`, `closed_at`, `last_message_at`
  - `chat_messages`: `id`, `session_id`, `user_id` nullable, `guest_uuid` nullable, `sender_role`, `content`, `image_path`, `is_read`, `created_at`
- Thiết kế order giữ tương đương nghiệp vụ hiện tại: quantity, note, total_price, status, stock deduction logic, audit timestamps.
- Viết một pipeline import bằng Artisan:
  1. Export dữ liệu PostgreSQL hiện tại từ Supabase.
  2. Map `auth.users` + `profiles` sang `users`.
  3. Map `roles/user_roles` sang bảng role của Laravel.
  4. Import `food_categories`, `foods`, `orders`, `visit_sessions`, `chat_sessions`, `chat_messages`.
  5. Mirror ảnh từ Supabase Storage sang filesystem đích.
  6. Rewrite URL ảnh thành path nội bộ mới.
- Cutover:
  1. Dựng app PHP trên staging.
  2. Chạy import thử.
  3. UAT toàn bộ flow.
  4. Freeze write trên app cũ.
  5. Chạy final import.
  6. Smoke test.
  7. Chuyển domain.
  8. Giữ hệ thống cũ ở chế độ read-only để rollback ngắn hạn.

## Các phase triển khai
1. **Foundation**
- Tạo Laravel app mới với Breeze Blade, Bootstrap 5.3, PostgreSQL, Redis, Reverb, queue, scheduler.
- Thiết lập layout Blade chung cho public và admin, giữ branding hiện tại nhưng chuyển sang component Bootstrap.
- Thiết lập middleware auth, guest, admin role, rate limit, upload validation, exception handling.

2. **Schema và domain model**
- Viết migration + model + policy + form request cho categories, foods, orders, visit sessions, chat sessions, chat messages.
- Chuyển toàn bộ business rule từ SQL trigger/RLS/Supabase check sang service layer, policy, middleware, DB transaction và model event của Laravel.
- Tạo index tương đương cho `chat_messages.session_id`, `chat_messages.created_at`, `foods.category_id`, `foods.is_available`.

3. **Auth và phân quyền**
- Dựng login/register user bằng Breeze.
- Dựng `/adminlogin` riêng nhưng dùng cùng guard session.
- Gán role admin bằng seed hoặc command.
- Thay `RoleGate/AuthGate` bằng middleware + Blade authorization directives.

4. **Public pages**
- Clone `/home`, `/gioi-thieu`, `/thuc-pham`, `/thuc-pham/{slug}` sang Blade + Bootstrap.
- Thay query client-side bằng controller/service truy vấn server-side.
- Giữ category filter, featured foods, stock/is_available logic, order form và order success feedback.

5. **Order system**
- Port order create flow sang controller/service transaction.
- Giữ kiểm tra stock, note, quantity validation, total price calculation, status flow admin.
- Port `/history` sang server-rendered page có filter/tracking order status.

6. **QR visit sessions và guest flow**
- Port admin QR generation, token expiry, session listing, disable/delete session.
- Dùng signed token + guest session cookie để guest mở lại đúng chat session.
- Giữ cơ chế guest name prompt và session persistence.

7. **Chat system**
- Public chat page:
  - load chat session theo logged-in user hoặc guest token
  - pagination 50 tin gần nhất
  - load thêm tin cũ
  - upload ảnh
  - send message
  - close-state handling
- Admin chat page:
  - danh sách session
  - chọn session mới load messages
  - trả lời chat
  - đóng session
  - xóa session/message nếu giữ parity đầy đủ
- Realtime:
  - broadcast event khi có message mới hoặc session status thay đổi
  - frontend subscribe qua Reverb/Echo
  - polling fallback 30 giây

8. **Admin CRUD**
- Port admin dashboard, foods, categories, orders, chat về controller + Blade + Bootstrap.
- Giữ search/filter/sort quan trọng ở food admin và order admin.
- Dùng controller actions + flash/toast feedback thay cho client-side Supabase mutation.

9. **Migration tooling và cutover**
- Tạo lệnh `php artisan import:universaltea`.
- Tạo smoke checklist chạy sau import.
- Tạo seed/admin bootstrap command.
- Tạo Docker Compose hoặc deploy manifest cho Nginx, PHP-FPM, PostgreSQL, Redis, queue worker, scheduler, Reverb.

## Test plan và acceptance
- Unit/feature tests bằng **Pest/PHPUnit** cho:
  - auth, admin login, role middleware
  - food/category CRUD
  - order create, stock deduction, cancel/refund
  - visit token validation, QR expiry
  - chat send/upload/close/delete
  - rate limit và file validation
- Browser/E2E tests bằng **Laravel Dusk** cho:
  - user browse catalog, view detail, place order, view history
  - guest scan QR, chat with store, attach image
  - admin login, view sessions, reply chat, close session, manage orders, manage foods/categories
- Acceptance criteria bắt buộc:
  - guest QR chat hoạt động không cần tài khoản
  - admin reply xuất hiện realtime ở phía guest
  - fallback polling vẫn nhận tin khi websocket down
  - order flow giữ đúng stock và trạng thái
  - ảnh food/chat upload được và hiển thị đúng
  - admin role mới truy cập được admin pages
  - dữ liệu migrate từ hệ cũ sang không mất session/order/message/ảnh

## Giả định và mặc định đã khóa
- Dùng **Laravel 11 + Blade + Bootstrap 5.3 + PostgreSQL + Redis + Reverb**.
- Deploy trên **VPS/Docker**, không tối ưu cho shared hosting.
- Mục tiêu là **functional clone đầy đủ**, không clone 1:1 giao diện Tailwind hiện tại.
- Giữ PostgreSQL, nhưng **không** giữ Supabase Auth/Realtime/Storage trong runtime đích.
- Ảnh v1 dùng `public` disk của Laravel; có thể đổi sang S3/MinIO sau mà không đổi interface.
- File đầu ra nên được lưu thành **`docs/php-bootstrap-clone-plan.md`** khi chuyển sang chế độ thực thi.

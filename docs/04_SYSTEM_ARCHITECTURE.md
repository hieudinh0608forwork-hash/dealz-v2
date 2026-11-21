# 04 – System Architecture

## 1. Định hướng
Modular monolith trên WordPress. WordPress cung cấp nền tảng CMS, tài khoản và REST framework; plugin `dealz-core` chứa nghiệp vụ; theme `dealz-theme` chịu trách nhiệm trình bày.

## 2. Logical components
- Identity & Access
- Destination CMS
- Service Catalog
- Trip Management
- Itinerary & AI Planner
- Budget Calculator
- Booking Engine
- Order Management
- Payment Adapter
- Partner Operations
- Notifications
- Reviews & Support
- Analytics

Các module có thể dùng chung một ứng dụng và database nhưng phải có ranh giới trách nhiệm rõ.

## 3. Quy tắc phụ thuộc
- Theme gọi API/service layer; không chứa nghiệp vụ giao dịch.
- REST controllers kiểm tra authentication, capability/ownership, input schema.
- Domain services thực hiện quy tắc nghiệp vụ.
- Repositories/data access đóng gói truy vấn.
- Adapters kết nối AI, map, routing, weather và payment.
- Không để module AI trực tiếp ghi trạng thái booking/payment.

## 4. Thành phần kỹ thuật định hướng
- CMS/backend: WordPress.
- Business logic: custom PHP plugin.
- Frontend: HTML/CSS/JavaScript; lựa chọn cách render cần quyết định sau audit.
- Database: MySQL tương thích phiên bản WordPress được chọn.
- API: WordPress REST API và custom routes.
- Map: MapLibre nếu phù hợp.
- Routing/geocoding/weather/LLM: adapter để có thể thay nhà cung cấp.
- Payment: abstraction; chưa chọn cổng chính thức.

## 5. Ranh giới dữ liệu cốt lõi
- User: tài khoản và hồ sơ.
- Partner: hồ sơ nhà cung cấp.
- Destination: điểm đến.
- Service: dịch vụ được cung cấp.
- Availability: khả năng cung ứng theo thời gian.
- Trip: chuyến đi do traveler sở hữu.
- Itinerary: phiên bản lịch trình của Trip.
- ItineraryItem: hoạt động trong lịch trình.
- Order: yêu cầu mua/đặt ở cấp giao dịch.
- Booking: phần đặt dịch vụ cụ thể, gắn dịch vụ/đối tác.
- Payment: lần thanh toán và trạng thái từ provider.
- LedgerEntry: bút toán tài chính có thể truy vết.

## 6. Security baseline
- Least privilege; kiểm tra ownership cho mọi tài nguyên riêng tư.
- Không tin dữ liệu giá/tổng tiền từ client.
- Secrets chỉ ở environment/config ngoài version control.
- Nonce/CSRF protection cho luồng phù hợp.
- Rate limiting cho đăng nhập, AI và endpoint nhạy cảm.
- Webhook xác minh chữ ký, timestamp nếu provider hỗ trợ, idempotency.
- Audit log cho hành động quản trị và thay đổi trạng thái tài chính.
- Không log token, mật khẩu, dữ liệu thanh toán nhạy cảm.

## 7. Môi trường
Cần kiểm kê local/staging/production, hosting, domain, PHP, WordPress, MySQL, SSL, backup, SMTP, cron và quyền triển khai trước khi chọn cấu hình cuối.

## 8. Quyết định chưa chốt
- Kiểu frontend (theme render truyền thống hay frontend tách riêng).
- Hosting và cách quản lý môi trường.
- Phiên bản WordPress/PHP/MySQL.
- Nhà cung cấp AI/map/routing/geocoding/weather/payment.
- Chiến lược cache, queue và media storage.

# Triển khai DealZ V2

Tài liệu này mô tả cài đặt thử nghiệm từ ZIP. Không dùng tài liệu này như phê duyệt production; PR #10 vẫn phải được chủ dự án duyệt riêng.

## 1. Yêu cầu tối thiểu

- WordPress 6.5 trở lên.
- PHP 8.1 trở lên; plugin và theme hiện khai báo `Requires PHP: 8.1`.
- MySQL 8.0 trở lên. Docker của dự án hiện chạy MySQL 8.4; MariaDB chưa được xác nhận trong bộ kiểm thử hiện tại.
- PHP extensions theo yêu cầu WordPress và MySQLi; HTTPS cần được bật ở môi trường có tài khoản thật.
- Hai gói cài đặt: `dealz-core.zip` (plugin) và `dealz-theme.zip` (theme), được tạo bởi workflow **Package DealZ installable ZIPs**.

## 2. Chuẩn bị và sao lưu

1. Dùng một WordPress sạch hoặc staging riêng; không cài thẳng lên production.
2. Sao lưu database và toàn bộ `wp-content` trước khi nâng cấp.
3. Lưu bản ZIP đang chạy để có thể quay lui.
4. Không đưa mật khẩu, salts, API key, token, thông tin DB hoặc file `.env` vào repository hay ZIP. Cấu hình bí mật qua biến môi trường/secret manager của máy chủ.
5. Kiểm tra phiên bản PHP, WordPress và MySQL trước khi cài.

## 3. Cài đặt từ ZIP

1. Trong WordPress Admin, mở **Plugins → Add New Plugin → Upload Plugin**.
2. Tải `dealz-core.zip`, cài đặt và **kích hoạt DealZ Core trước**. Plugin tạo/nâng cấp schema khi kích hoạt.
3. Mở **Appearance → Themes → Add New → Upload Theme**.
4. Tải `dealz-theme.zip`, cài đặt rồi kích hoạt DealZ Theme.
5. Không kích hoạt theme trước plugin. Nếu plugin không kích hoạt thành công hoặc báo lỗi migration, dừng lại và kiểm tra log máy chủ.

## 4. Kiểm tra sau cài đặt

### Endpoint health

Mở:

```text
https://<ten-mien>/wp-json/dealz/v1/health
```

Phản hồi hiện tại dự kiến có `service: dealz-core`, `status: scaffolded` và `application_ready: false`. HTTP 200 xác nhận route health của plugin trả lời; **không** có nghĩa toàn bộ ứng dụng đã sẵn sàng vận hành.

### Schema và 8 bảng

Chạy bằng WP-CLI từ thư mục WordPress:

```bash
wp option get dealz_db_version
wp db prefix
wp db query "SHOW TABLES LIKE '$(wp db prefix)dealz_%';"
```

- Giá trị schema mong đợi: `1.2.0`.
- Phải có đủ 8 bảng (thay `<prefix>` bằng prefix thực tế):
  - `<prefix>dealz_trips`
  - `<prefix>dealz_trip_collaborators`
  - `<prefix>dealz_itineraries`
  - `<prefix>dealz_itinerary_days`
  - `<prefix>dealz_itinerary_items`
  - `<prefix>dealz_budget_lines`
  - `<prefix>dealz_expenses`
  - `<prefix>dealz_route_legs`
- Nếu schema version không phải `1.2.0`, thiếu bảng, hoặc log có lỗi migration, không tiếp tục mở thử nghiệm.

## 5. Tài khoản khách và đăng ký

- **Không bật** WordPress **Settings → General → Membership → Anyone can register** (`users_can_register` phải giữ `false`).
- Đợt thử nghiệm đầu chỉ dùng tài khoản được quản trị viên/chủ dự án tạo theo lời mời và gửi thông tin truy cập qua kênh riêng an toàn.
- Không cung cấp luồng đăng ký công khai bằng trang `wp-login.php?action=register`.
- Đăng ký công khai chỉ được xem xét sau khi có trang tài khoản riêng, quy trình xác minh/bảo vệ phù hợp và chủ dự án duyệt rõ ràng. Không tự bật trong quá trình triển khai.

## 6. Quay lui

1. Đưa site vào chế độ bảo trì và ngừng nhận thao tác ghi.
2. Nếu chỉ có lỗi giao diện, cài lại ZIP theme phiên bản trước và kích hoạt lại theme.
3. Nếu lỗi liên quan plugin hoặc schema, cài lại ZIP plugin phiên bản trước. Không giả định schema mới có thể tự downgrade an toàn.
4. Khi cần quay về schema cũ, khôi phục **cả database snapshot và `wp-content` backup đồng bộ trước lần kích hoạt/nâng cấp**; không tự chạy câu lệnh DROP/ALTER để ép schema lùi.
5. Kiểm tra lại health endpoint, schema version, đủ 8 bảng và log PHP/WordPress trước khi tắt chế độ bảo trì.

## 7. Checklist trước khi mở cho người ngoài

- [ ] HTTPS hợp lệ và ép chuyển hướng HTTP → HTTPS.
- [ ] `users_can_register = false`; không có đăng ký công khai.
- [ ] Tài khoản thử nghiệm được tạo bằng lời mời; quyền truy cập tối thiểu.
- [ ] Bản staging được đặt noindex (WordPress Reading + robots/meta phù hợp); ưu tiên thêm lớp bảo vệ truy cập.
- [ ] Sao lưu database và `wp-content`; đã thử quy trình khôi phục.
- [ ] Health endpoint phản hồi; hiểu rõ `application_ready: false`.
- [ ] Schema là `1.2.0` và đủ 8 bảng.
- [ ] Không có mật khẩu, khóa, token hoặc dữ liệu bí mật trong repository, ZIP, log công khai hay ảnh QA.
- [ ] Chủ dự án duyệt nội dung, tài khoản được mời và phạm vi thử nghiệm trước khi chia sẻ URL.

**Trạng thái:** hướng dẫn cho môi trường thử nghiệm. Chưa phải xác nhận production-ready; không deploy production nếu chưa có phê duyệt riêng.


## 4.1 Pretty permalinks và REST health URL

- Sau khi cài WordPress, đặt **Settings → Permalinks** thành cấu trúc **Post name** (`/%postname%/`). Với WP-CLI có thể chạy `wp rewrite structure '/%postname%/' --hard` rồi `wp rewrite flush --hard`.
- REST URL chuẩn của DealZ là `/wp-json/dealz/v1/health`. WordPress có thể trả HTTP 301 để chuẩn hóa dấu gạch chéo cuối, ví dụ `/wp-json/dealz/v1/health` → `/wp-json/dealz/v1/health/`. Client kiểm tra health cần theo redirect và xác nhận response cuối là JSON đúng hợp đồng, không chỉ dựa vào HTTP 200.
- Nếu pretty permalinks chưa hoạt động, có thể chẩn đoán riêng bằng `/?rest_route=/dealz/v1/health`. Đây là đường kiểm tra phụ để phân biệt lỗi rewrite; không thay thế kiểm tra endpoint chuẩn `/wp-json/dealz/v1/health`.
- Health hiện trả `service: dealz-core`, `status: scaffolded`, `application_ready: false`; đây chỉ là xác nhận route/plugin phản hồi, không phải xác nhận ứng dụng sẵn sàng vận hành.

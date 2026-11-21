# DealZ – Quy tắc thiết kế (20/11/2026)

> Tài liệu này là nguồn chuẩn cho các quy tắc thiết kế giao diện. Nội dung này thay thế mục 11 của bản bàn giao trước. Không tự ý làm khác khi chưa có quyết định mới từ chủ dự án.

## 1. Lộ trình dạng thẻ

- Tối đa 4 điểm dừng chính/ngày. Bản hiện tại dựng 3 điểm + bữa trưa và bữa tối là thẻ nhỏ.
- Thẻ điểm dừng gồm: ảnh tỷ lệ 16:10, giờ đến, thời lượng gợi ý, góc chụp (gợi ý chung nếu chưa có dữ liệu), đồ gợi ý, nút "Thuê đồ cho điểm này", nút "Chỉ đường" (Google Maps).
- "Thuê đồ cho điểm này" mở trang Thuê đồ đã lọc theo phong cách, có liên kết "Quay lại [điểm dừng]".
- Đoạn nối giữa các điểm: đường nét liền + biểu tượng xe máy + nội dung dạng "45 phút · 32 km, ước tính". Chỉ hiển thị số liệu có nguồn; nếu chưa có dữ liệu routing thì không bịa số.
- Dải đầu ngày: tên điểm dừng, cuộn ngang. Có Lưu, Chia sẻ, In hoặc PDF.
- Ngoài Đà Nẵng – Hội An: hiện cảnh báo "ngoài khu thí điểm".
- **Cấm quay lại dải vòng tròn đánh số.**

## 2. Font

| Vai trò | Font | Cỡ, độ đậm |
|---|---|---|
| Tiêu đề lớn | Bricolage Grotesque | 800, 40–84px, dòng ~1.02, giãn −0.03em |
| Tiêu đề nhỏ, tên thẻ | Bricolage Grotesque | 700, 22–28px |
| Nội dung, menu, nút, ô nhập | Be Vietnam Pro | 400/600/700, 16–17px, dòng 1.6 |
| Giá, giờ | Be Vietnam Pro | 700, số đều (tabular-nums) |

- Tối đa 2 họ font, 4 độ đậm: 400, 600, 700, 800.
- File woff2 nằm trong theme; dùng `font-display: swap`, có font dự phòng, không gọi font từ mạng ngoài.
- Không in hoa nhãn; không tô màu riêng một từ trong tiêu đề.
- Dòng chữ tối đa khoảng 70 ký tự.
- Kiểm tra hiển thị chuỗi "Ướ ặ ẫ ỡ ọ" ở tiêu đề, nội dung và ô nhập.

## 3. Ô nhập, nút và thẻ

### Ô nhập
- Bo góc 14px, cao 52px, đệm 16px, nền trắng, viền 1.5px `#CFE0D8`, chữ tối thiểu 16px.
- Focus: viền `#FD550A` + quầng 4px cam nhạt.
- Lỗi: chữ đỏ + biểu tượng "!" dưới ô, viền đỏ.
- Nhãn trên ô: 14px, độ đậm 600, màu `#31483F`.
- Placeholder: `#7C948A` (thay `#8AA096` vì màu cũ chỉ đạt tương phản 2.8:1).
- Select có mũi tên tự vẽ; chip bo tròn; ô mật khẩu có Hiện/Ẩn; ngày chọn bằng bộ chọn hệ thống.

### Nút
- Nút chính: nền `#D63F00`, chữ trắng độ đậm 700, cỡ 17px, bo tròn hết, bóng mềm cam nhạt.
- `#D63F00` được chọn để đạt tương phản 4.5:1 với chữ trắng.
- `#FD550A` chỉ dùng cho hình khối, viền focus và nét nhấn.
- Mỗi màn hình chỉ có một nút chính.
- Có trạng thái đang gửi và vô hiệu.
- Cấm viền đen dày và bóng cứng.
- Bóng: `0 10px 30px` xanh rất nhạt.

### Thẻ
- Bo góc 22–28px, đệm 24px.
- Không dùng cùng một khuôn cho mọi loại thẻ:
  - Thẻ ảnh.
  - Thẻ tiền (nền xanh).
  - Thẻ ghi chú (vàng nhạt).
  - Thẻ cảnh báo (cam nhạt).

## 4. Liên kết và điều hướng
- Liên kết trong nội dung: xanh `#015D47`, gạch chân 2px, hover cam.
- Liên kết hành động: có mũi tên, dịch 4px khi hover.
- Menu: mục đang mở có nền vàng chanh; vòng focus rõ.
- Thanh điều hướng dưới điện thoại tối đa 5 mục, có biểu tượng + chữ; mục Hành trình có số đếm.
- Mỗi trang Khách kết thúc bằng khối "Tiếp theo".
- Thêm vào hành trình phải có thông báo kèm nút "Xem hành trình".
- Mỗi màn có địa chỉ riêng (ví dụ `#route`, `#shop`) để nút Back hoạt động.
- Chân trang có Chính sách cọc và hủy, Điều khoản, Bảo mật, Zalo; có nút Zalo nổi.
- Trạng thái trống luôn kèm việc nên làm và một nút hành động.

## 5. Màu sắc

| Vai trò | Màu |
|---|---|
| Cam nhấn | `#FD550A` |
| Nền nút chính / chữ cam | `#D63F00` |
| Xanh | `#015D47` |
| Vàng chanh | `#E0F354` |
| Kem | `#FBF7EE` |
| Chữ chính | `#0E2A22` |
| Chữ phụ | `#4C635A` |
| Viền | `#CFE0D8` |

Chữ trên nền vàng chanh luôn dùng xanh đậm. Màu chữ phụ `#4C635A` thay `#5A6E66`.

## 6. Không làm
- Không viền đen dày, không bóng cứng.
- Không dùng một khuôn thẻ cho mọi loại nội dung.
- Không dùng chữ hệ thống làm font chính.
- Không dùng dải vòng tròn đánh số.
- Không hiện thông tin liên hệ shop trước khi DealZ xác nhận.
- Không tự đưa giá hoặc hoa hồng đề xuất của nhóm vào sản phẩm khi chưa chốt.
- Không hiển thị tính năng chưa làm như thể đã hoạt động: thanh toán, tiếng Anh, AI.
- Không dùng số liệu giả trong cổng đối tác.

## 7. Quy tắc triển khai và kiểm tra
- Đây là tiêu chuẩn giao diện bắt buộc cho các màn hình thuộc phạm vi tài liệu này.
- Nếu chưa có dữ liệu thật (góc chụp, giá, thời gian/độ dài tuyến, số liệu đối tác), phải ghi rõ là gợi ý/ước tính hoặc không hiển thị; tuyệt đối không bịa.
- Không đưa tính năng AI, thanh toán hoặc tiếng Anh vào giao diện đang vận hành cho tới khi tính năng được triển khai và kiểm thử thật.
- Trước khi bàn giao giao diện, kiểm tra font tiếng Việt, responsive, trạng thái focus/error/loading/disabled/empty, tương phản và điều hướng Back.

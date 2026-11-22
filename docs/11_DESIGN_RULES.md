# DealZ – Quy tắc thiết kế (20/11/2026)

 > Tài liệu này là nguồn chuẩn cho các quy tắc thiết kế giao diện. Nội dung này thay thế mục 11 của bản bàn giao trước. Không tự ý làm khác khi chưa có quyết định mới từ chủ dự án.
>
> Phần 11.1–11.7 là **quy tắc chuẩn**. Phần 11.8 ghi riêng **tình trạng triển khai đã kiểm chứng**, không sửa đổi quy tắc.

## 11.1 Lộ trình dạng thẻ

- Tối đa 4 điểm dừng chính/ngày. Bản hiện tại dựng 3 điểm + bữa trưa và bữa tối là thẻ nhỏ.
- Thẻ điểm dừng gồm: ảnh tỷ lệ 16:10, giờ đến, thời lượng gợi ý, góc chụp (gợi ý chung nếu chưa có dữ liệu), đồ gợi ý, nút "Thuê đồ cho điểm này", nút "Chỉ đường" (Google Maps).
- "Thuê đồ cho điểm này" mở trang Thuê đồ đã lọc theo phong cách, có liên kết "Quay lại [điểm dừng]".
- Đoạn nối giữa các điểm: đường nét liền + biểu tượng xe máy + nội dung dạng "45 phút · 32 km, ước tính". Chỉ hiển thị số liệu có nguồn; nếu chưa có dữ liệu routing thì không bịa số.
- Dải đầu ngày: tên điểm dừng, cuộn ngang. Có Lưu, Chia sẻ, In hoặc PDF.
- Ngoài Đà Nẵng – Hội An: hiện cảnh báo "ngoài khu thí điểm".
- **Cấm quay lại dải vòng tròn đánh số.**

## 11.2 Font

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

## 11.3 Ô nhập, nút và thẻ

### 11.3.1 Ô nhập
- Bo góc 14px, cao 52px, đệm 16px, nền trắng, viền 1.5px `#7C948A`, chữ tối thiểu 16px.
- Đo xác nhận 22/11/2026: `#7C948A` đạt 3,25079:1 trên trắng, 3,04038:1 trên kem và 3,00095:1 trên mint-50. Hai ứng viên sáng lân cận `#7D958B` (2,96252:1) và `#7C948B` (2,99825:1) không đạt trên mint-50; giữ `#7C948A`.
- Placeholder `#5F776D` chỉ dùng trên nền input trắng (4,83:1). Focus: viền đặc `#FD550A` (3,24:1 trên trắng) + quầng 4px cam nhạt chỉ để trang trí. Outline ngoài 3px `#D63F00` đặc, offset 3px; trên nền forest dùng lime `#E0F354`.
- Lỗi: chữ đỏ + biểu tượng "!" dưới ô, viền đỏ.
- Nhãn trên ô: 14px, độ đậm 600, màu `#31483F`.
- Placeholder: `#5F776D` trên nền input trắng (4,83:1). Viền input riêng `#7C948A`; viền thẻ giữ `#CFE0D8`.
- Select có mũi tên tự vẽ; chip bo tròn; ô mật khẩu có Hiện/Ẩn; ngày chọn bằng bộ chọn hệ thống.

### 11.3.2 Nút
- Nút chính: nền `#D63F00`, chữ trắng độ đậm 700, cỡ 17px, bo tròn hết, bóng mềm cam nhạt.
- `#D63F00` được chọn để đạt tương phản 4.5:1 với chữ trắng.
- `#FD550A` chỉ dùng cho hình khối, viền focus và nét nhấn.
- Mỗi màn hình chỉ có một nút chính.
- Có trạng thái đang gửi và vô hiệu.
- Cấm viền đen dày và bóng cứng.
- Bóng: `0 10px 30px` xanh rất nhạt.

### 11.3.3 Thẻ
- Bo góc 22–28px, đệm 24px.
- Không dùng cùng một khuôn cho mọi loại thẻ:
  - Thẻ ảnh.
  - Thẻ tiền (nền xanh).
  - Thẻ ghi chú (vàng nhạt).
  - Thẻ cảnh báo (cam nhạt).

## 11.4 Liên kết và điều hướng
- Liên kết trong nội dung: xanh `#015D47`, gạch chân 2px, hover cam.
- Liên kết hành động: có mũi tên, dịch 4px khi hover.
- Menu: mục đang mở có nền vàng chanh; vòng focus rõ.
- Thanh điều hướng dưới điện thoại tối đa 5 mục, có biểu tượng + chữ; mục Hành trình có số đếm.
- Mỗi trang Khách kết thúc bằng khối "Tiếp theo".
- Thêm vào hành trình phải có thông báo kèm nút "Xem hành trình".
- Mỗi màn có địa chỉ riêng (ví dụ `#route`, `#shop`) để nút Back hoạt động.
- Chân trang có Chính sách cọc và hủy, Điều khoản, Bảo mật, Zalo; có nút Zalo nổi.
- Trạng thái trống luôn kèm việc nên làm và một nút hành động.

## 11.5 Màu sắc

| Vai trò | Màu |
|---|---|
| Cam nhấn | `#FD550A` |
| Nền nút chính | `#D63F00` (chữ trắng) |
| Xanh | `#015D47` |
| Vàng chanh | `#E0F354` |
| Kem | `#FBF7EE` |
| Chữ chính | `#0E2A22` |
| Chữ phụ | `#4C635A` |
| Chữ cam trên nền sáng | `#C43A00` |
| Viền thẻ | `#CFE0D8` |
| Viền input | `#7C948A` |

Chữ trên nền vàng chanh luôn dùng xanh đậm. Màu chữ phụ `#4C635A` thay `#5A6E66`.

## 11.6 Không làm
- Không viền đen dày, không bóng cứng.
- Không dùng một khuôn thẻ cho mọi loại nội dung.
- Không dùng chữ hệ thống làm font chính.
- Không dùng dải vòng tròn đánh số.
- Không hiện thông tin liên hệ shop trước khi DealZ xác nhận.
- Không tự đưa giá hoặc hoa hồng đề xuất của nhóm vào sản phẩm khi chưa chốt.
- Không hiển thị tính năng chưa làm như thể đã hoạt động: thanh toán, tiếng Anh, AI.
- Không dùng số liệu giả trong cổng đối tác.

## 11.7 Quy tắc triển khai và kiểm tra
- Đây là tiêu chuẩn giao diện bắt buộc cho các màn hình thuộc phạm vi tài liệu này.
- Nếu chưa có dữ liệu thật (góc chụp, giá, thời gian/độ dài tuyến, số liệu đối tác), phải ghi rõ là gợi ý/ước tính hoặc không hiển thị; tuyệt đối không bịa.
- Không đưa tính năng AI, thanh toán hoặc tiếng Anh vào giao diện đang vận hành cho tới khi tính năng được triển khai và kiểm thử thật.
- Trước khi bàn giao giao diện, kiểm tra font tiếng Việt, responsive, trạng thái focus/error/loading/disabled/empty, tương phản và điều hướng Back.


## 11.8 Tình trạng hiện tại (22/11/2026)

Các ghi nhận dưới đây là trạng thái triển khai tại nhánh `work/ui-rental-cta-browser-migration-2026-11-22`, không phải quy tắc mới:

| Hạng mục | Tình trạng | Còn thiếu / giới hạn |
|---|---|---|
| CSS design tokens | Token màu tập trung tại `assets/css/tokens.css`; CI có gate cấm hex ngoài file này và gate ma trận tương phản | Cần giữ các token semantic đồng bộ khi thêm component |
| Font | Bricolage Grotesque 700/800 và Be Vietnam Pro 400/600/700 đã tải từ Google Fonts, subset latin/latin-ext/vietnamese, WOFF2 và OFL được lưu cục bộ | Be Vietnam Pro không có OpenType `tnum`; tổng WOFF2 279.284 bytes (272,74 KiB), cao hơn mục tiêu xấp xỉ 220 KiB. Không tự đổi font |
| App shell | Header sticky, mobile nav island, footer và 3 trang legal draft noindex; Zalo chỉ hiện khi HTTPS option được cấu hình | Chưa có URL Zalo do chủ dự án chưa cung cấp |
| Itinerary | Rail ngày, timeline, empty rental 7/5 và giới hạn 4 điểm dừng chính/ngày đã được triển khai; badge đếm stop chưa lưu trữ | Badge/meal behavior cần xác nhận với chủ dự án; chưa có ảnh thật/giấy phép |
| Browser QA | Workflow Chromium desktop/mobile và artifact screenshot tồn tại | Chưa có bằng chứng Firefox, Safari, thiết bị thật hoặc screen reader |

Không suy diễn trạng thái `Implemented`, `Tested` hoặc `Verified` ngoài những gì có log hoặc artifact gắn với commit cụ thể.


## 11.9 Ma trận tương phản đã chốt

- Ink, ink-2, label đạt ≥4,5:1 trên trắng, kem, mint-50, mint-100, lime-50, orange-50.
- Placeholder #5F776D chỉ dùng trên input trắng; chữ cam #C43A00 dùng trên trắng/kem/mint-50/lime-50/orange-50, trên mint-100 chỉ khi chữ lớn.
- Nút chính #D63F00 dùng chữ trắng; không dùng chữ kem trên nền nút (4,30:1). Chữ trắng/kem trên forest lần lượt 7,90:1 / 7,39:1.
- Viền input #7C948A đạt tối thiểu 3,00095:1 với nền xung quanh mint-50; input có nền trắng. Không đổi sang #6F887D. Viền thẻ #CFE0D8 không đổi.
- Viền focus input #FD550A đạt 3,24:1 trên trắng; quầng alpha 4px chỉ trang trí. Outline ngoài #D63F00 đặc đạt ≥3,90:1 trên nền sáng; trên forest dùng lime (6,44:1).
- Xem docs/design-contrast.md và scripts/check-design-contrast.php để có ma trận và gate CI.


### 11.2.1 Số tiền và giờ – kết quả đo font
- FontTools đo Be Vietnam Pro 400 Latin WOFF2: `0–9 = 676, 385, 651, 660, 710, 660, 680, 594, 634, 680` (unitsPerEm 1000); không tabular. Giữ Be Vietnam Pro, không đổi font. Cột giờ `.item-time` và cột tiền `.budget-summary__tile strong`, `.finance-row__side strong` phải có `min-width` và căn phải; không dựa vào `tnum`.

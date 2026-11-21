# 03 – Design System

Nguồn chuẩn và đầy đủ: `docs/11_DESIGN_RULES.md`. Khi có khác biệt, tuân theo tài liệu quy tắc thiết kế ngày 20/11/2026.

## 1. Màu sắc

| Token | Value | Usage |
|---|---|---|
| brand.orange | #FD550A | Hình khối, viền focus, nét nhấn |
| brand.button | #D63F00 | Nền nút chính; chữ trắng |
| brand.green | #015D47 | Màu xanh nhận diện, liên kết |
| brand.lime | #E0F354 | Vàng chanh; chữ trên nền này dùng xanh đậm |
| surface.cream | #FBF7EE | Nền kem |
| text.primary | #0E2A22 | Chữ chính |
| text.secondary | #4C635A | Chữ phụ |
| text.label | #31483F | Nhãn ô nhập |
| text.placeholder | #7C948A | Placeholder |
| border.default | #CFE0D8 | Viền ô nhập |

## 2. Font
- Tiêu đề lớn: Bricolage Grotesque, 800, 40–84px, line-height khoảng 1.02, letter-spacing −0.03em.
- Tiêu đề nhỏ/tên thẻ: Bricolage Grotesque, 700, 22–28px.
- Nội dung/menu/nút/ô nhập: Be Vietnam Pro, 400/600/700, 16–17px, line-height 1.6.
- Giá/giờ: Be Vietnam Pro 700, dùng tabular numerals.
- Tối đa 2 họ font và 4 độ đậm: 400, 600, 700, 800.
- Font woff2 lưu trong theme, `font-display: swap`, có fallback, không gọi mạng ngoài.
- Không in hoa nhãn; không tô riêng một từ trong tiêu đề.
- Dòng chữ tối đa khoảng 70 ký tự.
- Kiểm tra chuỗi tiếng Việt "Ướ ặ ẫ ỡ ọ" ở tiêu đề, nội dung và input.

## 3. Form controls
- Input: radius 14px, cao 52px, padding 16px, nền trắng, border 1.5px #CFE0D8, font tối thiểu 16px.
- Focus: border #FD550A và focus ring 4px cam nhạt.
- Error: viền đỏ, chữ đỏ và biểu tượng ! bên dưới.
- Label: 14px/600/#31483F.
- Placeholder: #7C948A.
- Select dùng mũi tên tự vẽ; chip bo tròn; password có Hiện/Ẩn; date dùng bộ chọn hệ thống.

## 4. Buttons
- Primary: #D63F00, chữ trắng 17px/700, pill radius, bóng mềm cam nhạt.
- #D63F00 phải giữ tương phản tối thiểu 4.5:1 với chữ trắng.
- #FD550A không dùng làm nền nút chính với chữ trắng; chỉ dùng cho hình khối, focus và nét nhấn.
- Mỗi màn có tối đa một nút chính.
- Có trạng thái submitting và disabled.
- Không viền đen dày, không bóng cứng.

## 5. Cards
- Radius 22–28px, padding 24px.
- Không áp một khuôn duy nhất cho mọi thẻ.
- Thẻ ảnh, thẻ tiền (nền xanh), thẻ ghi chú (vàng nhạt), thẻ cảnh báo (cam nhạt) có cách thể hiện riêng.
- Thẻ điểm dừng dùng ảnh tỷ lệ 16:10 và các trường thông tin theo UX Blueprint.

## 6. Links và navigation
- Liên kết nội dung: #015D47, underline 2px, hover cam.
- Liên kết hành động có mũi tên và dịch 4px khi hover.
- Menu active có nền vàng chanh; focus ring rõ.
- Mobile bottom navigation tối đa 5 mục, có icon + label; Hành trình có count.
- Mỗi trang Khách kết thúc bằng "Tiếp theo".
- Thêm vào hành trình có thông báo và nút "Xem hành trình".
- Mỗi màn có route riêng để Back hoạt động.
- Footer: Chính sách cọc và hủy, Điều khoản, Bảo mật, Zalo; nút Zalo nổi.

## 7. Shadows, borders và spacing
- Card radius 22–28px; padding 24px.
- Input radius 14px.
- Shadow tham chiếu: 0 10px 30px, xanh rất nhạt.
- Không dùng viền đen dày hoặc bóng cứng.
- Tránh lặp một kiểu card cho mọi nội dung.

## 8. Accessibility và QA
- Focus state luôn nhìn thấy.
- Keyboard navigation và label cho form.
- Không dùng màu làm tín hiệu duy nhất.
- Kiểm tra tương phản, responsive và zoom.
- Kiểm tra font tiếng Việt bằng chuỗi quy định.
- Kiểm tra loading, error, disabled, empty và trạng thái gửi.
- Chi tiết và ngưỡng bắt buộc xem `docs/11_DESIGN_RULES.md`.

## 9. Các điều cấm
- Không dùng font hệ thống làm font chính.
- Không in hoa nhãn.
- Không tô màu riêng một từ trong tiêu đề.
- Không dùng dải vòng tròn đánh số.
- Không dùng liên hệ shop trước khi DealZ xác nhận.
- Không đưa giá/hoa hồng nhóm đề xuất chưa chốt vào giao diện.
- Không giả lập thanh toán, tiếng Anh, AI khi chưa triển khai.
- Không dùng số liệu giả trong cổng đối tác.

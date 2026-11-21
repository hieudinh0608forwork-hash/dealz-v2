# DealZ V2 – Quyết định thiết kế và phạm vi đã chốt
Ngày: 2026-11-22

> Khi DECISIONS.md khác UI_BRIEF.md thì DECISIONS.md thắng.

Tài liệu này ghi lại các quyết định do chủ dự án chốt. Không tự diễn giải thành chính sách mới. Các nội dung không được quyết định ở đây tiếp tục tuân theo UI_BRIEF.md và phải hỏi chủ dự án khi có điểm mơ hồ thuộc phạm vi cần phê duyệt.

## 1. Màu placeholder
- Placeholder: `#5F776D`.
- Tỷ lệ tương phản tham chiếu đã chốt: 4,83:1 trên nền trắng.

## 2. Viền ô nhập và viền thẻ
- Viền ô nhập: `#7C948A`, tỷ lệ tương phản tham chiếu đã chốt 3,25:1.
- Viền thẻ tiếp tục dùng `#CFE0D8`.

## 3. Màu cam
- Chữ cam trên nền kem: `#C43A00`, tỷ lệ tương phản tham chiếu đã chốt 4,97:1.
- Nền nút chính giữ `#D63F00`; chữ trắng trên nền này có tỷ lệ tương phản tham chiếu 4,59:1.

## 4. Focus của ô nhập
- Vòng focus bên ngoài: outline đặc 3px `#D63F00`, offset 3px.
- Trên nền xanh, dùng vòng lime.
- Viền ô nhập khi focus: `#FD550A`.
- Không dùng alpha làm chỉ báo focus duy nhất.

## 5. Đo tương phản trên các nền thực tế
- Phải đo lại mọi cặp màu trên tất cả nền: trắng, kem, mint-50, mint-100, lime-50, orange-50 và forest.
- Ngưỡng: chữ thường tối thiểu 4,5:1; viền ô nhập và focus tối thiểu 3:1.
- Cặp màu dưới ngưỡng phải được sửa và báo chủ dự án.
- Cập nhật `docs/design-contrast.md`, `docs/11_DESIGN_RULES.md` và Master Handoff trong cùng một commit.
- Không tuyên bố số đo mới nếu chưa thực sự đo bằng công cụ.

## 6. Font và giấy phép
- Chỉ tải font một lần trong giai đoạn phát triển từ nguồn chính thức Google Fonts hoặc repository `google/fonts`; commit tệp vào `assets/fonts/`.
- Không gọi mạng ngoài để tải font khi site chạy.
- Font yêu cầu:
  - Bricolage Grotesque: variable hoặc các mức 700, 800.
  - Be Vietnam Pro: 400, 600, 700.
  - WOFF2, các subset latin, latin-ext và vietnamese.
  - Kèm giấy phép OFL và ghi nguồn trong `docs/third-party.md`.
- Nếu font thiếu glyph tiếng Việt hoặc tính năng `tnum`, báo chủ dự án; không tự đổi font.

## 7. Zalo
- URL Zalo chỉ lấy từ tùy chọn `dealz_zalo_url`.
- Khi chưa cấu hình tùy chọn, ẩn liên kết Zalo và nút nổi liên quan.
- Không tự tạo hoặc suy đoán URL.

## 8. Trang pháp lý
- Ba trang: Chính sách cọc và hủy, Điều khoản, Bảo mật chỉ là bản nháp, ghi rõ “chưa áp dụng” và đặt noindex.
- Không đưa tỷ lệ cọc, tỷ lệ hoàn, giờ hủy.
- Không điền tên công ty, mã số thuế hoặc địa chỉ pháp lý.

## 9. Phạm vi và nguyên tắc an toàn
- Không thay đổi chính sách cọc/hủy/hoa hồng khi chưa có quyết định của chủ dự án.
- Không tạo dữ liệu giả về sản phẩm, giá, shop, ảnh địa điểm, đánh giá, số km/phút hoặc thống kê.
- Không thêm phụ thuộc mạng ngoài khi site chạy.
- Giữ PR #10 ở trạng thái Draft; không merge main và không deploy trong phạm vi công việc này.

## 10. Ma trận tương phản đã đo (22/11/2026)
- Các cặp đang dùng và ngưỡng CI được liệt kê trong `docs/design-contrast.md`.
- Placeholder `#5F776D` chỉ dùng trên nền input trắng: 4,83:1.
- Viền input `#7C948A`: 3,25:1 so với nền input trắng; 3,04:1 so với kem; 3,00095:1 so với mint-50. Đạt ngưỡng 3:1 trên các nền ngoài được phép, không đổi sang `#6F887D`.
- Viền focus input `#FD550A` trên trắng: 3,24:1. Quầng 4px alpha 0,16 composite thành `#FFE4D8`, 1,21:1; chỉ trang trí, không thay viền đặc.
- Outline focus `#D63F00` đạt tối thiểu 3,90:1 trên nền sáng; trên forest dùng lime `#E0F354`, đạt 6,44:1.
- Chữ kem trên nút `#D63F00` chỉ đạt 4,30:1 nên bị loại; nút chính dùng chữ trắng (4,59:1). Chữ cam `#C43A00` trên mint-100 chỉ dùng cho chữ lớn (4,51:1).

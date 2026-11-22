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
- Riêng CTA biểu mẫu `#create-item-submit` dùng `--dealz-primary-hover` `#B93400` với chữ trắng (5,91:1) để tăng dư địa tương phản; không thay đổi token primary toàn cục.

## 4. Focus của ô nhập
- Vòng focus bên ngoài: outline đặc 3px `#D63F00`, offset 3px.
- Trên nền xanh, dùng vòng lime.
- Viền ô nhập khi focus: `#FD550A`.
- Không dùng alpha làm chỉ báo focus duy nhất.

## 5. Đo tương phản trên các nền thực tế
- Chỉ đo các cặp màu thực sự được dùng trong giao diện theo ma trận đã chốt tại docs/design-contrast.md; không tạo phép đo giả cho các cặp không xuất hiện trong layout.
- Ngưỡng: chữ thường tối thiểu 4,5:1; chữ lớn tối thiểu 3:1; viền ô nhập và focus tối thiểu 3:1.
- Cặp đang dùng dưới ngưỡng phải được sửa và báo chủ dự án; cặp không dùng được ghi rõ là bị loại khỏi ma trận.
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
- Đo thực tế từ WOFF2 đã tải: Be Vietnam Pro không có OpenType `tnum`; tổng WOFF2 là 279.284 bytes (272,74 KiB), cao hơn mục tiêu xấp xỉ 220 KiB. Giữ đúng họ font đã chốt; không tự thay thế.

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

Ma trận hiện hành chỉ đo các cặp thực sự dùng trong UI, theo yêu cầu cập nhật của chủ dự án; không áp dụng phép đo mọi token trên mọi nền.
- Các cặp đang dùng và ngưỡng CI được liệt kê trong `docs/design-contrast.md`.
- Placeholder `#5F776D` chỉ dùng trên nền input trắng: 4,83:1.
- Viền input `#7C948A`: 3,25:1 so với nền input trắng; 3,04:1 so với kem; 3,00095:1 so với mint-50. Đạt ngưỡng 3:1 trên các nền ngoài được phép, không đổi sang `#6F887D`.
- Viền focus input `#FD550A` trên trắng: 3,24:1. Quầng 4px alpha 0,16 composite thành `#FFE4D8`, 1,21:1; chỉ trang trí, không thay viền đặc.
- Outline focus `#D63F00` đạt tối thiểu 3,90:1 trên nền sáng; trên forest dùng lime `#E0F354`, đạt 6,44:1.
- Chữ kem trên nút `#D63F00` chỉ đạt 4,30:1 nên bị loại; nút chính dùng chữ trắng (4,59:1). Chữ cam `#C43A00` trên mint-100 chỉ dùng cho chữ lớn (4,51:1).


## 11. Master Handoff và tiêu chuẩn quy trình (22/11/2026)
- `docs/13_HANDOFF_COMPLETION.md` là tiêu chuẩn quy trình hoàn thành, KHÔNG phải Master Handoff và KHÔNG được dùng làm nguồn sự thật dự án.
- Master Handoff chính thức sẽ nằm tại `docs/MASTER_HANDOFF.md`; chưa có file này thì ghi nhận là chưa tạo, không suy diễn nội dung từ tiêu chuẩn quy trình.

## 12. Giới hạn bốn điểm dừng chính mỗi ngày
- Chỉ đếm mục có loại `Điểm dừng chính`.
- Mục `Bữa ăn` và `Thẻ nhỏ` không tính vào giới hạn.
- Mục đã lưu trữ không tính.
- Dữ liệu cũ vượt giới hạn không bị sửa, xóa hoặc tự lưu trữ; chỉ chặn thao tác thêm mới khi ngày đó đã có từ bốn điểm dừng chính chưa lưu trữ.
- Áp dụng nhất quán tại REST API, OpenAPI, UI và integration/browser tests.

## 13. Định nghĩa badge Hành trình
- Badge là tổng số điểm dừng chưa lưu trữ của chuyến đi đang được chọn.
- Badge ẩn khi tổng bằng 0.
- Không cộng bữa ăn hoặc thẻ nhỏ nếu chúng không phải loại `Điểm dừng chính`; badge vẫn đếm mọi điểm dừng chưa lưu trữ theo định nghĩa này. (Nếu implementation hiện tại dùng mọi `stop`, phải đối chiếu model và sửa cho đúng định nghĩa loại.)

## 14. Be Vietnam Pro và số tabular
- Không đổi họ font Be Vietnam Pro chỉ vì thiếu OpenType `tnum`.
- Dùng fontTools đọc advance width của glyph số 0–9 từ WOFF2 đang dùng và ghi kết quả đo; chỉ kết luận tabular sẵn nếu mười advance width bằng nhau.
- Nếu advance width không bằng nhau, giữ font; các trường số tiền và giờ phải có cột `min-width` phù hợp và căn phải để ổn định bố cục.
- Không khai báo hỗ trợ `tnum` giả.

## 15. Ngân sách font và subset
- Tổng WOFF2 mục tiêu tối đa 300 KiB.
- Chỉ preload hai tệp font.
- Dùng `unicode-range` cho subset.
- Thử loại subset latin-ext trước, giữ latin và vietnamese; kiểm tra toàn bộ ký tự cần dùng trong nội dung, tiêu đề, biểu mẫu và tiếng Việt trước khi chấp nhận thay đổi.
- Không bỏ glyph cần thiết để đạt ngân sách; nếu thiếu ký tự, giữ subset phù hợp và ghi rõ số đo.

## 16. Viền ô nhập
- Đo màu viền thực tế trên cả ba nền input có thể dùng: trắng, kem và mint-50.
- Chọn giá trị tối thiểu đạt tương phản ít nhất 3:1 trên cả ba nền; `#7C948A` là ứng viên đã có số đo cũ, `#6F887D` chỉ là ước tính cần đo, không được ghi là kết quả.
- Ghi công thức/giá trị đo, token cuối cùng và cập nhật `docs/design-contrast.md`, `docs/11_DESIGN_RULES.md` trong cùng commit khi đổi token.


### 16.1 Kết quả đo viền input đã đối chiếu (22/11/2026)
- Đo lại bằng công thức WCAG relative luminance trên sRGB: `#7C948A` / trắng `#FFFFFF` = **3,25079:1**; / kem `#FBF7EE` = **3,04038:1**; / mint-50 `#F2F7F4` = **3,00095:1**.
- Thử làm sáng lân cận: `#7D958B` đạt 2,96252:1 trên mint-50; `#7C948B` đạt 2,99825:1. Cả hai không đạt 3:1. Giữ `#7C948A` làm giá trị sáng nhất trong các ứng viên lân cận đã đo; không đổi token.


### 14.1 Kết quả đo fontTools và subset (22/11/2026)
- WOFF2 Be Vietnam Pro 400 Latin: `unitsPerEm=1000`; advance width glyph `0–9`: `0=676, 1=385, 2=651, 3=660, 4=710, 5=660, 6=680, 7=594, 8=634, 9=680`. Có 8 giá trị khác nhau; font KHÔNG tabular sẵn. Giữ Be Vietnam Pro; `.item-time`, `.budget-summary__tile strong`, `.finance-row__side strong` dùng `min-width` và căn phải, không giả định `tnum`.
- Đã bỏ subset latin-ext sau khi quét 12 file nguồn PHP/JS/CSS/HTML: không có codepoint nào chỉ được latin-ext hỗ trợ mà còn cần dùng. Giữ latin + vietnamese; CSS font-face khai báo `unicode-range`.
- Tổng 10 file WOFF2 hiện tại: **201.432 bytes (196,71 KiB)**, dưới giới hạn 300 KiB. Chỉ preload 2 file Vietnamese quan trọng: Bricolage Grotesque 800 và Be Vietnam Pro 400. `font-proof.json` ghi `latin_ext_removed=true`, tổng byte và kết quả coverage.
- Các ký tự ngoài cả latin/vietnamese subset vẫn dùng font fallback; icon giao diện ưu tiên SVG inline, không giả định font subset có glyph đó.
- Bằng chứng đo: `assets/fonts/font-proof.json`, `scripts/check-font-metrics.py`, `scripts/check-font-subsets.py`.


## 17. Tài khoản khách trong giai đoạn thử nghiệm (22/11/2026)
- Giữ `users_can_register = false`; không bật đăng ký công khai mặc định của WordPress.
- Đợt thử nghiệm đầu chỉ sử dụng tài khoản do quản trị viên/chủ dự án tạo theo lời mời.
- Chỉ xem xét đăng ký công khai khi có trang tài khoản riêng và chủ dự án duyệt rõ ràng.
- Quyết định này áp dụng cho cả staging và checklist triển khai; không tự tạo endpoint/form đăng ký công khai.

LỆNH TỰ ĐỘNG: NÂNG GIAO DIỆN DEALZ V2 LÊN MỨC CAO CẤP VÀ HOÀN THIỆN PR #10

=== 0. VAI TRÒ VÀ GIỚI HẠN ===
Bạn là người triển khai DealZ V2 (repo hieudinh0608forwork-hash/dealz-v2). Tôi là người kiểm soát. Giao tiếp tiếng Việt. Tôi chỉ chấp nhận "xong" khi có bằng chứng chạy được.

Việc đầu tiên: lưu nguyên văn lệnh này vào docs/UI_BRIEF.md (commit riêng). Đầu mỗi nhiệm vụ, đọc lại docs/UI_BRIEF.md, docs/HANDOFF.md, docs/design-rules.md. Thiếu file nào thì ghi vào báo cáo và đi tiếp.

Nhánh: work/ui-rental-cta-browser-migration-2026-11-22 (PR #10, giữ Draft).
KHÔNG: merge main, deploy, phát sinh chi phí hoặc API ngoài, đổi chính sách cọc/hủy/hoa hồng, gọi font/ảnh/script từ mạng ngoài, che lỗi bằng cách tắt cảnh báo hoặc test.
KHÔNG DỮ LIỆU GIẢ: sản phẩm, giá, shop, ảnh địa điểm, đánh giá, logo đối tác, số km/phút, thống kê, lời chứng thực.
Tính năng chưa làm (thanh toán, AI, tiếng Anh, đặt thuê, chia sẻ, PDF) thì ẩn hoặc ghi "Sắp có". Không để nút chết.

=== 1. CÁCH LÀM VIỆC ===
- Chạy liên tục T0→T8, không dừng báo cáo giữa chừng.
- Mỗi nhiệm vụ: sửa → chạy test liên quan → commit riêng (tiền tố feat/fix/docs/test + một dòng mô tả) → push → gh run watch. CI đỏ thì đọc log, sửa, push lại, tối đa 5 lần cho mỗi lỗi.
- Giữ nguyên selector, data-testid, ARIA mà test hiện có đang dùng. Buộc phải đổi thì cập nhật test cùng commit và nói rõ trong báo cáo.
- Không thêm dependency chạy ở frontend (không framework, không CDN). Dev dependency cho test (Playwright, axe-core) được phép. Theo cấu trúc CSS/JS hiện có; nếu chưa có thì dùng assets/css/{tokens,base,components,pages}.css.
- Dừng sớm CHỈ khi: (a) cần quyết định chính sách, chi phí, pháp lý; (b) hai tài liệu mâu thuẫn; (c) một lỗi vẫn đỏ sau 5 lần; (d) thiếu thứ không được tự bịa: URL Zalo, ảnh thật có giấy phép, font không tải được. Với (d), làm tiếp mọi phần không phụ thuộc và ghi vào mục "Cần tôi cung cấp" của báo cáo.

=== 2. TINH THẦN THIẾT KẾ: "CAO CẤP, ẤM, BIÊN TẬP" ===
Cảm giác: tạp chí du lịch in cao cấp kết hợp khách sạn boutique. Không sao chép bố cục của thương hiệu nào. Tránh hình ảnh sáo rỗng (đèn lồng, nón lá) trừ khi là ảnh thật.
Mười nguyên tắc:
1. Khoảng trắng trước, viền sau. Ưu tiên tách nhóm bằng khoảng cách rồi mới tới đường kẻ 1px.
2. Mỗi màn có một khoảnh khắc "lớn" (một tiêu đề lớn hoặc một khối ảnh), phần còn lại yên tĩnh.
3. Tỷ lệ màu theo diện tích: kem/trắng ~70%, xanh rừng ~20%, vàng chanh ~7%, cam ~3%. Cam chỉ ở nút chính (một cái mỗi màn), nét nhấn nhỏ và vòng focus. Vàng chanh chỉ cho trạng thái đang chọn/nhấn mạnh, chữ trên vàng luôn xanh đậm, không đặt cam lên vàng.
4. Chữ là nhân vật chính: tiêu đề Bricolage lớn, giãn chặt; giờ và tiền dùng số tabular rõ.
5. Mọi cạnh canh theo lưới 4px. Lề trái tiêu đề trùng lề trái nội dung.
6. Bo góc lồng nhau: bán kính trong ≤ bán kính ngoài − đệm.
7. Bóng mềm ngả xanh, không bóng đen, không viền dày.
8. Chuyển động nhỏ, có mục đích, tôn trọng prefers-reduced-motion.
9. Mỗi trạng thái đều được thiết kế: default, hover, focus-visible, active, disabled, loading, empty, error, success.
10. Trung thực tuyệt đối về nội dung và dữ liệu.
CẤM: viền đen dày, bóng cứng, dải vòng tròn đánh số, in hoa nhãn, tô màu riêng một từ trong tiêu đề, emoji làm icon, gradient tím/xanh dương, glassmorphism ngoài header và thanh dưới, hiệu ứng neon, ảnh stock hotlink, "5★" hay "bán chạy" giả, hiện liên hệ shop trước khi DealZ xác nhận.

=== 3. HỆ THỐNG THIẾT KẾ ===
3.1 Màu. Một file tokens.css duy nhất; nơi khác chỉ dùng var(). CI có script kiểm tra: cấm mã hex ngoài tokens.css (trừ docs và tệp ảnh).
Giá trị dưới đây là DỰ KIẾN. Tự đo bằng công cụ, ghi docs/design-contrast.md. Số đo khác thì dùng số đo và báo tôi. Chỉ được thêm tint (độ sáng) của màu đã duyệt, không thêm sắc mới.
 --cream #FBF7EE (nền trang) · --paper #FFFFFF · --ink #0E2A22 (chữ) · --ink-2 #4C635A (chữ phụ) · --label #31483F
 --forest #015D47 · --mint-50 #F2F7F4 · --mint-100 #E4EFE9 · --line #CFE0D8 (viền thẻ, đường kẻ)
 --line-strong ~#7C948A (viền ô nhập) · --placeholder ~#5F776D
 --orange #FD550A (hình khối, focus, nét nhấn) · --orange-ink #D63F00 (nền nút chính, chữ cam) · --orange-50 ~#FFEEE6 · --orange-200 ~#FFD3B8
 --lime #E0F354 · --lime-50 ~#F7FAD8 · --error #B42318 · --error-50 #FEF3F2
Mục tiêu tương phản: chữ thường ≥4,5:1; chữ lớn (≥24px, hoặc ≥19px đậm) ≥3:1; viền ô nhập và vòng focus ≥3:1. Kiểm tra cả chữ cam trên kem, chữ phụ trên mint/lime-50, chữ mờ trên thẻ xanh và trên khung ảnh rỗng.

3.2 Chữ.
- Bricolage Grotesque (700, 800) cho tiêu đề; Be Vietnam Pro (400, 600, 700) cho nội dung, nút, ô nhập, giá, giờ. Tối đa 2 họ, 4 độ đậm.
- File woff2 tự lưu trong theme, subset latin + latin-ext + vietnamese, kèm file giấy phép OFL, font-display: swap, preload 2 file quan trọng. Bricolage dùng bản variable (trục wght) hoặc 2 file tĩnh, chọn phương án nhẹ hơn. Tổng font ≤ ~220KB.
- Tạo font dự phòng có size-adjust/ascent-override để giảm CLS. Không dùng chữ hệ thống làm font chính.
- Kiểm chứng Be Vietnam Pro có tnum. Nếu không có, báo tôi, không tự đổi font.
- Nếu không tải được font: dừng phần font, báo tôi, KHÔNG thay bằng font khác.
Thang chữ:
 display: clamp(44px, 6.2vw + 8px, 84px) / 1.02 / 800 / -0.03em, max-width 14ch, text-wrap: balance
 h1: clamp(36px, 4vw + 12px, 56px) / 1.05 / 800 / -0.025em
 h2: clamp(28px, 2.4vw + 14px, 40px) / 1.1 / 700 / -0.02em
 h3 (tên thẻ): clamp(22px, 1vw + 18px, 28px) / 1.2 / 700 / -0.01em
 body-lg 18/1.6 · body 17/1.6 (mobile 16) · small 15/1.5 · label 14/1.4/600
 số (giờ, tiền): Be Vietnam Pro 700, tabular-nums; tiền lớn trong thẻ tiền tới 44px
Quy tắc: câu thường (không in hoa nhãn); không chữ nào <14px (ngoại lệ duy nhất: số trong badge, khai báo trong tokens); độ rộng dòng ≤68ch; không canh giữa đoạn >3 dòng.
Định dạng vi-VN: tiền bằng Intl.NumberFormat("vi-VN") → "1.200.000 ₫"; ngày dd/mm/yyyy; thứ viết đầy đủ ("Thứ Năm").

3.3 Khoảng cách và lưới.
- Thang 4px: 4 8 12 16 24 32 48 64 96 128.
- Lề trang 20 (mobile) / 32 (≥768) / 48 (≥1100). Container nội dung 1200, rộng 1320. Lưới 12 cột, gutter 16 (mobile) / 24 (desktop).
- Nhịp dọc giữa các khối: 56–72 (mobile), 96–128 (desktop).
- Đệm thẻ 24; thẻ lớn (hero, thẻ tiền, empty state) 32 trên desktop. Ghi điều chỉnh này vào mục "Điều chỉnh" của design-rules.
- Breakpoint CSS: 640, 768, 1024, 1280. Kiểm thử: 360, 390, 768, 1024, 1280, 1536.

3.4 Hình khối, bóng, chất liệu.
- Bo: ô nhập 14; thẻ 22/24/28; chip và nút 999.
- Bóng (ngả xanh): sh-1 0 2px 8px rgba(1,93,71,.06) · sh-2 0 10px 30px rgba(1,93,71,.08) · sh-3 0 18px 48px rgba(1,93,71,.14) · sh-cta 0 8px 20px rgba(214,63,0,.22).
- Viền thẻ 1px var(--line).
- Chất liệu: nhiễu giấy SVG inline (data URI ≤1.5KB, opacity ~.035) trên nền hero và chân trang. Bỏ nếu làm giảm tương phản hoặc tăng LCP.
- Họa tiết đường đồng mức (contour) tự vẽ bằng SVG inline, stroke 1px, opacity thấp, aria-hidden, ≤3KB mỗi file. Dùng cho hero, khung ảnh rỗng, chân trang. Đây là bản sắc "bản đồ" của DealZ, không phải dữ liệu.

3.5 Icon. Một bộ duy nhất, stroke 1.75px, lưới 24px, đầu bo tròn, inline SVG sprite trong theme (không icon font, không CDN). Cần: compass, route, hanger, user, clock, map-pin, navigation/arrow-up-right, motorbike, camera, shirt, chevron-up, chevron-down, arrow-right, check, alert, plus, pencil, calendar, wallet, archive. Dùng bộ có sẵn (Lucide ISC / Phosphor MIT) thì ghi nguồn và giấy phép vào docs/third-party.md.

3.6 Chuyển động.
- Easing cubic-bezier(.2,.7,.2,1). Thời lượng: 90ms (nhấn), 180ms (hover/chọn), 280ms (mở panel, toast), 360ms (vào trang).
- Vào trang: opacity 0→1 + translateY 12px→0, stagger 60ms, tối đa 6 phần tử, chỉ lần đầu.
- Hover thẻ: nâng 2px, sh-2→sh-3. Nút: nhấn scale .98. Liên kết hành động: mũi tên dịch 4px. Skeleton shimmer 1.4s.
- Cấm parallax, nhấp nháy, animation >400ms, animation lặp vô hạn (trừ skeleton và spinner).
- prefers-reduced-motion: tắt mọi chuyển động, chỉ giữ đổi màu.

=== 4. THÀNH PHẦN ===
4.1 Nút. Cao 52 (lớn) / 44 (nhỏ), chữ Be Vietnam Pro 700, bo pill. Mỗi loại một class và thuộc tính data-btn.
- primary: nền --orange-ink, chữ trắng, sh-cta. Hover: đậm nhẹ + nâng 1px. Active: scale .98. Disabled: nền mint-100, chữ ink-2 (không dùng opacity). Loading: spinner 18px, giữ độ rộng, aria-busy, nhãn "Đang lưu…".
- secondary: nền paper, viền 1.5px forest, chữ forest.
- tonal: nền mint-100, chữ forest, có mũi tên (dùng cho "Thuê đồ cho điểm này" trong thẻ điểm dừng).
- tertiary: chữ forest 700, có mũi tên, dịch 4px khi hover.
- icon-button: 44×44, tròn, aria-label bắt buộc.
Focus-visible: vòng 3px --orange, offset 3px (trên nền xanh dùng vòng lime). Tối đa MỘT nút primary hiển thị mỗi màn. Primary theo màn:
 - Trang chủ: "Tạo chuyến đi".
 - Hành trình: "Thêm vào ngày này" (form thêm điểm dừng). Các nút gửi form khác ("Thêm ngày", "Thêm khoản dự kiến", "Lưu khoản chi", "Lưu thay đổi") là secondary. Nếu chưa có chuyến thì primary là "Tạo chuyến đi"; có chuyến chưa có ngày thì là "Thêm ngày".
 - Thuê đồ trống: "Quay lại [điểm dừng]".

4.2 Ô nhập (theo mục 5.2 của quy tắc). Cao 52, bo 14, đệm 16, nền paper, viền 1.5px --line-strong, chữ ≥16px.
- Nhãn luôn hiện phía trên (14px, 600, --label). Dòng trợ giúp 14px --ink-2 bên dưới.
- Focus: viền --orange + quầng 4px rgba(253,85,10,.16).
- Lỗi: viền --error, chữ --error + icon "!" bên dưới, aria-invalid, aria-describedby.
- Select: mũi tên tự vẽ, appearance none. Số: inputmode numeric. Ngày: bộ chọn hệ thống.
- Giờ 24 giờ trên mọi trình duyệt. Nếu <input type=time> không đảm bảo, dùng ô có mặt nạ HH:MM (pattern, inputmode numeric) hoặc 2 select giờ/phút. Kiểm chứng với locale en-US và vi-VN.
- Giờ chưa đặt hiển thị "Chưa đặt giờ" (ink-2), tuyệt đối không "00:00".

4.3 Chip. Cao 36–44, pill. Mặc định mint-100/ink; đang chọn: nền lime + chữ forest 700; có thể kèm số đếm. Chip không bấm được thì không có hover. Chip loại: "Điểm dừng chính" (mint), "Bữa ăn" (lime-50).

4.4 Bốn loại thẻ, mỗi loại một component có cấu trúc riêng, không chỉ khác màu:
a. Thẻ điểm dừng/ảnh: paper, viền line, r24, sh-1, ảnh tràn mép trên, đệm nội dung 24.
b. Thẻ tiền: nền --forest, chữ trắng/kem, r28, không viền. Gồm Dự kiến / Đã chi / Còn lại (số 700 tabular, tới 44px), thanh tiến độ (track trắng 18%, fill lime). Khi âm hiện "Vượt ngân sách" bằng chip cam nhạt + icon "!" (không chỉ dựa vào màu). Số liệu chỉ lấy từ dữ liệu thật; chưa có thì "—" kèm dòng "Thêm khoản chi dự kiến đầu tiên".
c. Thẻ ghi chú: nền --lime-50, r22, icon bút, chữ ink. Dùng cho "Gợi ý chung".
d. Thẻ cảnh báo: nền --orange-50, viền --orange-200, tile icon "!" nền orange-ink, r22, có tiêu đề + mô tả + hành động tertiary. Dùng cho: ngoài khu thí điểm, đủ 4 điểm dừng chính, lỗi API (kèm "Thử lại").

4.5 Khung ảnh (ImageSlot).
- Có ảnh thật (chỉ khi có media xác minh và có giấy phép/ghi công): <img> có width/height, 16:10 (thẻ điểm dừng) hoặc 4:5 (hero), object-fit cover, loading lazy (trừ ảnh LCP), decoding async, alt mô tả thật, ghi công 14px dưới ảnh.
- Chưa có ảnh: biến thể gọn, aspect 16:6, min-height 112px (mobile) / 144px (desktop). Nền gradient từ forest đến ink, đường đồng mức stroke rgba(224,243,84,.16), icon map-pin lime 28px, tên điểm dừng Bricolage 700 22px trắng ở dưới trái, dòng "Chưa có ảnh đã xác minh" 14px ở dưới phải. role="img", aria-label="Chưa có ảnh cho [tên]". Không dùng ảnh stock hay ảnh AI giả làm ảnh địa điểm thật. Ghi điều chỉnh "khung rỗng dùng biến thể gọn, ảnh thật dùng 16:10" vào design-rules.

4.6 Header. Cao 72 (mobile 64), nền cream. Khi cuộn >8px: nền rgba(251,247,238,.86) + backdrop-filter blur(14px) + đường kẻ line (có nền đặc dự phòng). Logo: ô bo 12 nền lime chữ "D" Bricolage 800 forest + "DealZ" Bricolage 800 forest. Menu 16/600; mục đang mở là pill lime chữ forest, aria-current="page". "Tài khoản" là nút secondary nhỏ. Thanh admin WP: top = var(--wp-admin--admin-bar--height, 0px). Xử lý cả ≤600px (thanh admin cuộn đi): kiểm chứng bằng ảnh ở 390px sau khi cuộn, và ở desktop khi đăng nhập admin.

4.7 Thanh dưới mobile (≤767px): "đảo nổi".
- left/right 12px + safe-area, bottom 12px + env(safe-area-inset-bottom), cao 64, r28, nền rgba(255,255,255,.9) + blur(16px) (nền đặc dự phòng), viền 1px line, sh-2.
- 3 đến 5 mục: Khám phá, Hành trình, Tài khoản (thêm Thuê đồ nếu route đã có). Icon 24 + nhãn 14px. Mục đang mở là pill lime chữ forest 700.
- Badge số đếm trên "Hành trình": nền forest, chữ trắng, tối thiểu 20×20, chỉ hiện khi >0, aria-label đầy đủ. Số đếm = số điểm dừng chưa lưu trữ của chuyến đang chọn; ghi định nghĩa vào docs và nêu trong báo cáo để tôi xác nhận.
- body padding-bottom và scroll-padding-bottom = chiều cao nav + 24 để ô nhập không bị che khi focus. Nút Zalo nổi nằm trên nav 16px, không chồng.

4.8 Chân trang. Nền --forest, chữ cam kem, họa tiết đường đồng mức mờ, chữ "DealZ" cỡ rất lớn bị cắt mép dưới làm trang trí (aria-hidden).
- Cột 1: thương hiệu + "Đang thí điểm tại Đà Nẵng – Hội An".
- Cột 2: Chính sách cọc và hủy · Điều khoản · Bảo mật (3 trang bản nháp).
- Cột 3: Hỗ trợ (Zalo).
- Dòng cuối giữ nguyên lời tuyên bố "sản phẩm đang trong quá trình phát triển".
- Zalo: lấy URL từ tùy chọn theme dealz_zalo_url. Chưa cấu hình thì ẩn cả liên kết chân trang và nút nổi. TUYỆT ĐỐI không tự bịa số hay đường dẫn.
- Ba trang bản nháp: noindex, thẻ cảnh báo "Bản nháp, chưa có hiệu lực", chỉ liệt kê các mục dự kiến. KHÔNG đưa tỷ lệ cọc, hoàn, giờ hủy. KHÔNG điền tên công ty, mã số thuế, địa chỉ pháp lý.

4.9 Toast: role="status", aria-live="polite". Vị trí: trên nav mobile, góc phải dưới desktop. Thành công: paper + viền mint + icon check. Lỗi: error-50 + viền error + icon "!" + nút "Thử lại", không tự ẩn. Có nút hành động tertiary ("Xem hành trình"). Tự ẩn sau 5s (trừ lỗi).

4.10 Skeleton: cream→mint shimmer, đúng kích thước thẻ thật (không gây CLS), aria-busy trên vùng đang tải.

4.11 Empty state: tile icon 64 nền lime, h3, 1–2 dòng (nêu rõ vì sao trống + việc nên làm), đúng một nút.

4.12 Đoạn nối giữa các điểm dừng: đường dọc nét liền 2px (forest ~25%), chip pill paper viền line ở giữa với icon xe máy + "45 phút · 32 km, ước tính" CHỈ khi có dữ liệu tuyến thật; chưa có thì "Chưa có đủ dữ liệu tuyến đường".

4.13 Trang styleguide: chỉ cho admin đăng nhập, noindex, không nằm trong menu. Liệt kê token, thang chữ, mọi trạng thái của nút/ô nhập/chip/thẻ/toast/skeleton/empty/ImageSlot/đoạn nối. Dùng để QA và để tôi duyệt một lần.

=== 5. BỐ CỤC TỪNG MÀN ===
5.1 Trang chủ.
- Hero 2 cột ≥1024 (7/5). Trái: eyebrow 14/600 forest với chấm lime "Đang thí điểm tại Đà Nẵng – Hội An"; tiêu đề display "Lên kế hoạch cho chuyến đi của bạn." (không tô màu một từ, không dòng mồ côi); đoạn dẫn body-lg ≤52ch; nút primary "Tạo chuyến đi ↗"; dòng nhỏ về việc đăng nhập (giữ nguyên nội dung chữ hiện có, chỉ chỉnh bố cục).
- Phải: minh họa 4:5, r28, nền forest, đường đồng mức, đĩa lime, đường chấm nối 3 ghim, chip nổi "Đà Nẵng – Hội An". aria-hidden, không số liệu. Mobile: minh họa dưới tiêu đề, aspect 4:3.
- Dưới hero: bento 12 cột, 4 thẻ khác cỡ và khác khuôn, chỉ mô tả tính năng ĐÃ chạy thật (đọc code và test để xác nhận từng câu): tạo chuyến đi và sắp xếp ngày (mint-50, mini-timeline minh họa), ngân sách (thẻ forest, minh họa thanh, KHÔNG số), chỉ đường tới Google Maps, và "Thuê đồ theo điểm dừng" với chip "Sắp có", không có nút (thẻ ghi chú).
- Cuối trang: khối "Tiếp theo" nền forest, liên kết tertiary "Tạo chuyến đi →".

5.2 Hành trình.
- Đầu trang (không đóng khung): liên kết "← Quay lại hành trình", eyebrow "Chuyến đi đang chọn", h1 tên chuyến đi, hàng meta (ngày đi–về, số ngày, chip trạng thái). Nút Lưu/Chia sẻ/In hoặc PDF chỉ hiện khi chức năng đã chạy thật.
- Desktop ≥1100, 2 cột:
 · Rail trái 4/12, sticky (top = header + 24): thẻ tiền; danh sách ngày dạng hàng 56px ("Ngày 1" 700, dd/mm, số điểm dừng nhỏ, chấm lime khi chọn; KHÔNG vòng tròn đánh số); thẻ ghi chú "Gợi ý chung" (tùy chọn).
 · Cột chính 8/12: h2 ngày + dải chip tên điểm dừng cuộn ngang (scroll-snap, mờ hai mép, bấm để cuộn tới thẻ, scroll-margin-top đủ lớn) → timeline → thẻ form thêm điểm dừng → khối ngân sách chi tiết → khối "Tiếp theo".
- Mobile: một cột. Chip ngày cuộn ngang, sticky dưới header (tính cả thanh admin); rồi dải chip điểm dừng; timeline; thẻ tiền; form; nav đảo nổi.
- Timeline: desktop có cột giờ rộng 88px (giờ 700 tabular 20px), đường dọc 2px, nốt 12px (lime, viền forest 2px, không số). Mobile: giờ nằm trong thẻ, trên cùng bên phải.
- Thẻ điểm dừng (dọc ở mọi viewport), thứ tự: ImageSlot → hàng chip loại + giờ đến → h3 tên → meta (icon đồng hồ + "90 phút gợi ý"; icon ghim + địa chỉ, tối đa 2 dòng) → "Góc chụp" và "Đồ gợi ý" (icon + 1–2 dòng; chưa có dữ liệu thì thẻ ghi chú nhỏ gắn nhãn "Gợi ý chung") → hàng hành động: [Thuê đồ cho điểm này → tonal] [Chỉ đường ↗ tertiary, Google Maps, target _blank rel noopener] + bên phải 2 icon-button ↑ ↓ (aria-label, disabled ở đầu/cuối) + "Chỉnh sửa" (tertiary).
- Panel chỉnh sửa: <details> có style, lưới 2 cột (mobile 1 cột), mọi ô có nhãn thấy được (kể cả "Thời lượng (phút)"). Chân panel: nút "Lưu thay đổi" (secondary, full-width trên mobile) và "Lưu trữ điểm dừng" (tertiary, màu ink-2, KHÔNG đỏ). "Lưu trữ" mở <dialog> xác nhận, mô tả ĐÚNG hành vi thực tế trong code. Không hứa khôi phục nếu chưa có chức năng khôi phục.
- Form thêm điểm dừng: thẻ riêng, lưới 2 cột, nhãn đầy đủ, primary "Thêm vào ngày này" có trạng thái đang gửi, lỗi inline cạnh ô.
- Ngân sách chi tiết: 2 thẻ paper (Dự kiến | Đã chi). Mỗi hàng: tên + chip nhóm + số tiền căn phải tabular; tổng ở cuối sau đường kẻ. Nút "Thêm khoản …" (secondary) mở form trong <details>. Empty: icon + 1 dòng + nút secondary.
- Thẻ cảnh báo "ngoài khu thí điểm Đà Nẵng – Hội An": chỉ khi địa chỉ/toạ độ xác định được. Không đoán.

5.3 Thuê đồ (trạng thái trống).
- Đầu trang: chip ngữ cảnh pill mint "Từ hành trình · Ngày 1 · 09:00" (chỉ dữ liệu thật); eyebrow "Thuê đồ theo điểm dừng"; h1 "Thuê đồ cho [tên điểm dừng]" (text-wrap: balance). Bỏ khoảng trống thừa phía trên.
- Thân, 2 cột ≥1024 (7/5):
 · Trái: thẻ empty lớn (mint-50, r28, đệm 32): tile hanger lime 64, h2 "Danh mục thuê đồ chưa sẵn sàng", 2 đoạn giữ đúng nội dung trung thực hiện có, nút primary DUY NHẤT "Quay lại [điểm dừng]".
 · Phải: thẻ ghi chú "Gợi ý chung khi chụp ảnh" với 3 ý ngắn (ánh sáng, màu trang phục tương phản với nền, trang phục thoải mái khi đi xe máy), nhãn "Gợi ý chung, chưa dựa trên dữ liệu điểm dừng". Nếu Zalo đã cấu hình thì thêm tertiary "Nhắn Zalo để được báo khi có shop →".
- Cuối trang: khối "Tiếp theo" (forest) dẫn tới "Xem toàn bộ hành trình →".
- Tuyệt đối không có sản phẩm, giá, shop, tồn kho, liên hệ shop.

=== 6. NGÔN NGỮ GIAO DIỆN ===
Xưng "bạn". Câu thường. Nhãn nút bắt đầu bằng động từ. Lỗi nói rõ chuyện gì đã xảy ra và làm gì tiếp, không nói chung chung, không lặp "Vui lòng".
Thuật ngữ cố định: Chuyến đi (trip) · Lịch trình (theo ngày) · Hành trình (mục điều hướng, nơi xem chuyến đi và lịch trình) · Điểm dừng · Khoản chi dự kiến · Khoản đã chi. Không đổi lẫn lộn.
Không dùng so sánh hơn hoặc quảng cáo không kiểm chứng được ("tốt nhất", "số 1").

=== 7. NHIỆM VỤ ===
T0. Chuẩn bị: lưu docs/UI_BRIEF.md; chạy toàn bộ test hiện có (phải còn 21 tests/120 assertions PASS); chụp ảnh gốc 360/390/768/1024/1280/1536 cho trang chủ, hành trình (có ngày + 2 điểm dừng + vài khoản chi), panel chỉnh sửa mở, thuê đồ trống → docs/qa-baseline/.
T1. Migration: hết cảnh báo "Duplicate key name 'trip_status'" và "'day_type_status'". Phủ 1.0.0→1.2.0 và 1.1.0→1.2.0. Chạy migration 2 lần vẫn sạch. Test so sánh SHOW INDEX trước/sau lần chạy thứ hai và dữ liệu mẫu không đổi. CI fail nếu log chứa "Duplicate key". Không tắt báo lỗi.
T2. Nền tảng: tokens.css, font (kèm bằng chứng nạp), base, icon sprite, motion, docs/design-contrast.md, mục "Điều chỉnh" và đánh lại số mục 11.x trong design-rules, script CI cấm hex ngoài tokens.
T3. Thành phần (mục 4.1–4.5, 4.9–4.12) + trang styleguide (4.13). Mọi trạng thái hiển thị trong styleguide.
T4. Khung ứng dụng: header, nav mobile, chân trang + 3 trang bản nháp + Zalo qua tùy chọn, skip link, toast, skeleton.
T5. Màn hình: trang chủ, hành trình, thuê đồ trống theo mục 5.
T6. Logic:
 a. Giờ chưa đặt + giờ 24h.
 b. Giới hạn tối đa 4 điểm dừng chính mỗi ngày ở REST API (mã lỗi rõ ràng, thêm vào OpenAPI) và UI (thẻ cảnh báo, vô hiệu tùy chọn loại "Điểm dừng chính"). Dữ liệu cũ đã vượt giới hạn thì KHÔNG sửa hay xóa, chỉ chặn thêm mới. Thêm ≥2 integration test (đạt/chạm giới hạn, mục đã lưu trữ không tính). Cách tính bữa ăn trong giới hạn đang mơ hồ: nếu code hiện tại không rõ, hỏi tôi.
 c. Badge đếm Hành trình.
 d. Loại dữ liệu thử nghiệm ("QA ...") khỏi bản cài sạch: fixture chỉ chạy ở môi trường test; Docker fresh install phải không chứa chuỗi "QA".
 e. Tạo docs/image-shot-list.md: danh sách ảnh cần có cho từng loại điểm dừng (tỉ lệ 16:10 ≥1600×1000 và 4:5, chủ thể, góc chụp), kèm mẫu media-credits.json (nguồn, tác giả, giấy phép). Chỉ ảnh do đội chụp hoặc có giấy phép rõ ràng.
T7. Cổng chất lượng (Playwright desktop+mobile, Chromium; chạy ở mọi viewport trong T0). Các test sau phải PASS trong CI:
 - Test cũ (21/120) vẫn PASS.
 - Không tràn ngang: scrollWidth ≤ innerWidth ở mọi viewport.
 - Chữ hiển thị ≥14px (trừ ngoại lệ đã khai báo). Control tương tác ≥44×44px (trừ liên kết nằm trong đoạn văn).
 - axe-core: 0 lỗi serious/critical trên mọi màn.
 - Bàn phím: Tab qua toàn bộ control theo thứ tự hợp lý; mọi control có vòng focus nhìn thấy khi :focus-visible (kiểm computed style).
 - Font: document.fonts.check cho "800 40px 'Bricolage Grotesque'" và "400 17px 'Be Vietnam Pro'" đều true; chuỗi "Ướ ặ ẫ ỡ ọ" hiển thị đúng ở tiêu đề, nội dung, ô nhập.
 - Không có request tới origin khác (bắt page.on('request')).
 - Đếm nút primary hiển thị mỗi màn ≤ 1.
 - Không xuất hiện "00:00" cho giờ chưa đặt; ô giờ đúng 24h ở locale en-US và vi-VN.
 - Admin bar: header không bị che (đăng nhập admin, desktop và 390px sau khi cuộn).
 - prefers-reduced-motion: không còn animation.
 - CLS <0.1 (gate); LCP báo cáo, không gate.
 - Luồng: CTA điểm dừng → Thuê đồ → Quay lại (desktop VÀ mobile); loading/lỗi API/thử lại; sắp xếp; sửa; lưu trữ có xác nhận.
 - Docker fresh install không chứa "QA", không có Zalo giả.
 Xuất ảnh vào artifact theo tên màn-viewport-trạng thái.png.
T8. Tự chấm "cao cấp" và sửa:
 Chấm 10 tiêu chí, mỗi tiêu chí 0–2 điểm, có ảnh làm bằng chứng: (1) thứ bậc thị giác, (2) nhịp khoảng trắng, (3) typography, (4) kiềm chế màu theo tỷ lệ, (5) nhất quán thành phần, (6) lưới và căn lề, (7) khung ảnh và họa tiết, (8) chuyển động, (9) đủ trạng thái, (10) giọng văn và trung thực.
 Yêu cầu tổng ≥16/20 và không tiêu chí nào 0. Chưa đạt thì quay lại sửa, tối đa 3 vòng. Nêu 3 điểm yếu nhất còn lại. Tôi sẽ chấm lại độc lập.

=== 8. BÁO CÁO CUỐI (đúng khuôn, chỉ gửi khi xong hoặc dừng theo mục 1) ===
1. Commit (hash + một dòng) theo T0–T8.
2. Link run CI và kết quả từng job.
3. Bảng: tiêu chí | cách đo | kết quả đo thật | trạng thái (Implemented/Tested/Verified).
4. docs/design-contrast.md: bảng tương phản đã đo và token nào đã đổi so với dự kiến.
5. Đường dẫn ảnh trước/sau theo màn và viewport; điểm tự chấm 10 tiêu chí.
6. "Cần tôi cung cấp": URL Zalo, ảnh thật + giấy phép, xác nhận định nghĩa badge, xác nhận cách đếm bữa ăn trong giới hạn 4 điểm dừng.
7. Việc chưa làm hoặc không chắc chắn, nói thẳng. Không tuyên bố "đã kiểm thử toàn hệ thống". Chưa test Safari, Firefox, thiết bị thật thì ghi rõ.
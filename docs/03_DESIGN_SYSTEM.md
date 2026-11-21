# 03 – Design System

## 1. Brand tokens

| Token | Value | Usage |
|---|---|---|
| color.brand.orange | #FD550A | CTA chính, điểm nhấn |
| color.brand.green | #015D47 | Nhận diện, nền đậm |
| color.brand.lime | #E0F354 | Điểm nhấn phụ |
| color.text.primary | TBD | Văn bản chính |
| color.text.secondary | TBD | Văn bản phụ |
| color.surface | TBD | Bề mặt |
| color.border | TBD | Đường viền |

Các token TBD phải được kiểm tra tương phản và chốt khi dựng giao diện; không tự suy diễn màu thương hiệu bổ sung.

## 2. Typography
- Heading: đậm, có cá tính, phân cấp rõ.
- Body: ưu tiên khả năng đọc ở kích thước mobile.
- Numeric: dùng định dạng thống nhất cho tiền, ngày và thời lượng.
- Font family cụ thể chưa được chốt.

## 3. Layout
- Mobile-first, responsive.
- Lưới và khoảng cách nhất quán.
- Nội dung chính ưu tiên trước hình trang trí.
- Card bo góc vừa phải; CTA nổi bật nhưng không lạm dụng.

## 4. Components cần có
- Button: primary, secondary, text, destructive.
- Form: input, select, date, textarea, validation.
- Card: destination, service, itinerary activity.
- Navigation: header, mobile navigation, breadcrumbs.
- Feedback: alert, toast, empty state, skeleton, error state.
- Data: price, status, timeline, map panel.
- Dialog: confirmation và destructive action.

## 5. Accessibility
- Có focus state nhìn thấy được.
- Hỗ trợ keyboard.
- Form có label và thông báo lỗi gắn với trường.
- Đảm bảo tương phản WCAG phù hợp.
- Không dùng màu làm tín hiệu duy nhất.
- Hỗ trợ zoom và kích thước vùng chạm phù hợp.

## 6. Implementation note
Design tokens phải được triển khai tập trung, không lặp mã màu tùy tiện trong từng component. Chưa có giao diện được implement hoặc kiểm thử tại thời điểm khởi tạo tài liệu.

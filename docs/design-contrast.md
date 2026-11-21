# DealZ – Báo cáo đo tương phản

Ngày đo: 22/11/2026  
Phạm vi: màu token hiện có trong `wp-content/themes/dealz-theme/style.css`.  
Phương pháp: WCAG relative luminance (sRGB linearization) và contrast ratio `(L1 + 0.05) / (L2 + 0.05)`; alpha color được composite trên nền trắng trước khi đo. Kết quả làm tròn 2 chữ số.

## Kết quả đo tại trạng thái hiện tại

| Cặp màu / vai trò | Tỷ lệ | Mục tiêu | Kết quả |
|---|---:|---:|---|
| Placeholder `#7C948A` trên trắng `#FFFFFF` | 3.25:1 | Chữ ≥ 4.5:1 | Không đạt |
| Chữ phụ `#4C635A` trên trắng | 6.48:1 | Chữ ≥ 4.5:1 | Đạt |
| Nút chính `#D63F00` với chữ trắng | 4.59:1 | Chữ ≥ 4.5:1 | Đạt |
| Chữ cam accent `#FD550A` trên kem `#FBF7EE` | 3.03:1 | Chữ ≥ 4.5:1 | Không đạt |
| Chữ `#D63F00` trên kem `#FBF7EE` | 4.30:1 | Chữ ≥ 4.5:1 | Không đạt |
| Viền ô nhập hiện tại `#CFE0D8` trên trắng | 1.37:1 | Viền ≥ 3:1 | Không đạt |
| Viền focus đặc `#FD550A` trên trắng | 3.24:1 | Focus ≥ 3:1 | Đạt |
| Outline focus alpha 0.35 trên trắng (composite) | 1.53:1 | Focus ≥ 3:1 | Không đạt |
| Viền thẻ `#CFE0D8` trên trắng | 1.37:1 | Không đặt mục tiêu ≥ 3:1 cho viền thẻ trong yêu cầu này | Giữ theo quy tắc hiện hành |

## Các giá trị tham chiếu để thảo luận

| Vai trò | Giá trị đang có | Ứng viên đo được | Kết quả ứng viên |
|---|---|---|---|
| Placeholder | `#7C948A` | `#5F776D` | 4.83:1 trên trắng; 4.52:1 trên kem |
| Viền ô nhập | `#CFE0D8` | `#7C948A` | 3.25:1 trên trắng |
| Chữ cam trên kem | `#FD550A` / `#D63F00` | `#C43A00` | 4.97:1 trên kem |
| Focus outline | `rgba(253, 85, 10, 0.35)` | `#FD550A` đặc | 3.24:1 trên trắng |

## Ghi chú quyết định

- Đây là phép đo tự động theo cặp màu nền đã nêu, không thay thế đánh giá mọi trạng thái component và nền thực tế.
- `#D63F00` đạt 4.59:1 với chữ trắng trên nút, nhưng chỉ đạt 4.30:1 khi dùng làm chữ trên nền kem; hai ngữ cảnh cần token riêng.
- Ứng viên màu chỉ là kết quả đo kỹ thuật. Không tự thay các giá trị đã chốt trong quy tắc thiết kế nếu chưa có quyết định rõ ràng khi chúng mâu thuẫn với yêu cầu cập nhật.

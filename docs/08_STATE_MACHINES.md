# 08 – State Machines

Các trạng thái dưới đây là khung kỹ thuật đề xuất; cần rà soát theo từng loại dịch vụ và chính sách đã được chủ dự án duyệt.

## 1. Trip
- draft
- planning
- planned
- in_progress
- completed
- archived

Chỉ owner hoặc admin được phép thay đổi; chuyển trạng thái phải phù hợp ngày đi và quy tắc sản phẩm.

## 2. Itinerary
- draft
- generated
- edited
- saved
- superseded

Một lần AI tạo mới không được âm thầm ghi đè bản người dùng đã chỉnh sửa. Lưu version và nguồn tạo.

## 3. Order (đề xuất)
- draft
- pending_payment
- partially_confirmed
- confirmed
- partially_cancelled
- cancelled
- completed
- payment_failed
- refund_pending
- partially_refunded
- refunded

Order tổng hợp trạng thái nhiều booking/payment; không thay thế trạng thái từng booking.

## 4. Booking (đề xuất)
- pending_partner
- pending_payment
- confirmed
- rejected
- cancelled
- completed
- no_show
- refund_pending
- refunded

Các chuyển đổi phụ thuộc loại dịch vụ, xác nhận đối tác, thanh toán và chính sách hủy/hoàn. Không triển khai state machine booking trước khi quy tắc tương ứng được phê duyệt.

## 5. Payment (đề xuất)
- created
- pending
- succeeded
- failed
- expired
- refund_pending
- partially_refunded
- refunded

Chỉ nguồn xác thực từ payment provider/backend mới được cập nhật trạng thái thanh toán. Redirect client không đủ căn cứ.

## 6. Audit
Mỗi transition cần lưu entity, entity_id, from_state, to_state, actor_type, actor_id, source, reason, timestamp và correlation/request ID.

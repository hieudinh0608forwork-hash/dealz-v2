# 09 – Test Plan

## 1. Mục tiêu
Xác minh chức năng, phân quyền, tính toàn vẹn dữ liệu, độ tin cậy tích hợp và trải nghiệm responsive trước khi phát hành.

## 2. Tầng kiểm thử
- Unit: domain services, validators, calculators, state transitions.
- Integration: WordPress hooks, repositories, REST endpoints, database.
- Contract: schema request/response và provider adapters.
- E2E: hành trình traveler/partner/admin.
- Security: authentication, authorization, ownership, input validation, CSRF, rate limit, webhook.
- Manual: responsive, accessibility, lỗi mạng, empty/loading/error.

## 3. Kịch bản ưu tiên
1. Traveler không thể đọc/sửa Trip của người khác.
2. Partner không thể truy cập dịch vụ/booking của partner khác.
3. Admin có quyền theo capability được cấp.
4. AI trả JSON sai schema thì không lưu.
5. AI timeout không làm mất input hoặc Trip hiện tại.
6. Chỉnh sửa itinerary tạo version phù hợp, không bị AI overwrite.
7. Budget calculator xử lý thiếu giá và currency khác nhau minh bạch.
8. Giá/availability thay đổi trước checkout phải được thông báo.
9. Tạo order lặp với cùng idempotency key không tạo bản ghi trùng.
10. Webhook sai chữ ký bị từ chối.
11. Webhook gửi lặp không tạo thanh toán/ledger trùng.
12. Hai yêu cầu đồng thời không vượt capacity.
13. Client không thể tự gửi tổng tiền tùy ý.
14. Giao diện hiển thị đúng loading/empty/error/success.
15. Layout hoạt động trên mobile, tablet, desktop.

## 4. Release gates
- Không còn lỗi blocker/critical chưa xử lý.
- Tất cả kiểm thử Must pass.
- Kiểm tra phân quyền và dữ liệu riêng tư pass.
- Có backup/rollback plan.
- Có cấu hình môi trường và secrets ngoài repository.
- Có log và hướng dẫn vận hành.
- Có xác nhận của chủ dự án cho production và chi phí liên quan.

## 5. Bằng chứng
Mỗi lần kiểm thử ghi: commit SHA, môi trường, phiên bản runtime, lệnh/kịch bản, kết quả, lỗi và người xác minh. Không ghi Tested/Verified nếu thiếu bằng chứng.

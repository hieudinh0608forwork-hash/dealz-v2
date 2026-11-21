# 06 – API Contract Baseline

## 1. Quy ước
- Base path dự kiến: `/wp-json/dealz/v1`.
- JSON request/response.
- Authentication theo WordPress session/cookie + nonce cho web session; token strategy chỉ quyết định khi có client use case.
- Mọi endpoint riêng tư phải kiểm tra quyền và ownership.
- Dùng HTTP status phù hợp; lỗi có code ổn định và message an toàn.
- Pagination dùng limit/cursor hoặc page thống nhất.
- Không nhận tổng tiền cuối cùng từ client làm nguồn tin cậy.

## 2. Endpoint dự kiến

| Method | Endpoint | Mục đích | Quyền |
|---|---|---|---|
| GET | /destinations | Danh sách điểm đến | Public |
| GET | /destinations/{id} | Chi tiết điểm đến | Public |
| GET | /services | Tìm dịch vụ | Public |
| GET | /services/{id} | Chi tiết dịch vụ | Public |
| GET | /me | Hồ sơ hiện tại | Authenticated |
| GET/POST | /trips | Liệt kê/tạo Trip | Traveler |
| GET/PATCH/DELETE | /trips/{id} | Xem/sửa/xóa Trip | Owner/Admin |
| POST | /trips/{id}/itineraries/generate | Yêu cầu AI tạo lịch trình | Owner |
| GET/POST | /trips/{id}/itineraries | Xem/lưu itinerary | Owner |
| PATCH/DELETE | /itinerary-items/{id} | Sửa/xóa hoạt động | Owner |
| POST | /budget/estimate | Tính dự toán | Authenticated/Guest policy TBD |
| POST | /orders | Khởi tạo order khi module booking được bật | Traveler |
| GET | /orders/{id} | Xem order | Owner/authorized admin |
| POST | /webhooks/{provider} | Nhận callback thanh toán | Provider signature |

## 3. Response conventions
Thành công: object dữ liệu có cấu trúc và metadata cần thiết.
Lỗi: `code`, `message`, `details` (chỉ dữ liệu không nhạy cảm), `request_id`.

## 4. AI itinerary request/response
Request cần có destination, date range, traveler count, budget/currency, preferences và constraints.
Response phải tuân theo schema itinerary đã version hóa; backend validate ngày, tọa độ, số lượng item, tổng thời lượng và dữ liệu không hợp lệ trước khi lưu.
AI output là nội dung gợi ý, không phải cam kết giá, tồn chỗ hoặc booking.

## 5. Idempotency và webhook
- Các thao tác tạo giao dịch phải hỗ trợ idempotency.
- Webhook xác minh chữ ký trước khi xử lý.
- Lưu event/provider reference để chống xử lý trùng.
- Xử lý bất đồng bộ nếu cần; retry không được tạo bút toán/booking lặp.
- Không tin redirect từ trình duyệt là bằng chứng thanh toán.

## 6. Trạng thái
Đây là contract sơ bộ. Request/response JSON Schema, error catalog, auth details và versioning policy cần hoàn thiện trước khi frontend tích hợp.

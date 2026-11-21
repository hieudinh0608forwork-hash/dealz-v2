# DealZ V2 – Contributor Instructions

## Project rules
- Đọc `README.md` và các tài liệu liên quan trong `docs/` trước khi thay đổi.
- DealZ V2 độc lập hoàn toàn với V1. Không sao chép mã nguồn, database, theme, plugin hay quy trình V1.
- Không tự ý thay đổi mô hình kinh doanh, tiền cọc, hoàn tiền, hoa hồng hoặc chính sách tài chính.
- Không thêm dịch vụ trả phí, khóa API thật hoặc bí mật vào repository.
- Không để AI tự xác nhận booking hoặc thanh toán.
- Giữ Trip, Order và Booking là các thực thể riêng.
- Backend là nguồn dữ liệu chính thức; frontend không tự quyết định trạng thái giao dịch.
- Ưu tiên modular monolith; không tách microservices sớm.
- Mọi endpoint phải xác thực, phân quyền, kiểm tra dữ liệu và ghi log phù hợp.
- Webhook phải xác thực chữ ký, chống xử lý trùng và có khả năng retry/an toàn.
- Không đánh dấu công việc Tested, Verified hoặc Deployed nếu chưa thực hiện kiểm chứng thực tế.

## Workflow
1. Xác định yêu cầu và tiêu chí chấp nhận.
2. Kiểm tra ảnh hưởng dữ liệu/API/quyền truy cập.
3. Triển khai thay đổi nhỏ, có thể kiểm thử.
4. Chạy kiểm thử phù hợp và ghi kết quả.
5. Cập nhật tài liệu và changelog.
6. Báo cáo chính xác trạng thái: Planned, In progress, Implemented, Tested, Verified hoặc Deployed.

## Code conventions
- PHP theo WordPress Coding Standards; ưu tiên namespace/prefix `DealZ\\` và `dealz_` cho hook/function cần prefix.
- Không đặt business logic trong theme.
- Không hard-code secrets, chính sách kinh doanh hoặc dữ liệu production.
- Escape output, sanitize input, validate dữ liệu; dùng capability checks và nonce khi phù hợp.
- REST API dùng schema/validation rõ ràng và response nhất quán.

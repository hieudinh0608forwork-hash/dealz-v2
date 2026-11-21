# 11 – Risk Register

| ID | Rủi ro | Mức độ | Giảm thiểu | Trạng thái |
|---|---|---|---|---|
| R-001 | Vô tình tái sử dụng mã nguồn/dữ liệu V1 | High | Repository sạch; review nguồn gốc mọi tài nguyên | Open |
| R-002 | Chưa biết hosting/runtime | High | Audit môi trường trước khi chọn cấu hình | Open |
| R-003 | AI tạo thông tin sai về địa điểm/giá | High | Schema validation, nguồn dữ liệu, nhãn ước tính, người dùng duyệt | Open |
| R-004 | Overbooking do race condition | High | Transaction/locking, kiểm tra capacity | Open |
| R-005 | Webhook giả mạo hoặc xử lý lặp | Critical | Signature verification, idempotency, audit | Open |
| R-006 | Chính sách tài chính chưa chốt | High | Không triển khai cọc/hoàn/commission/payout khi chưa duyệt | Open |
| R-007 | Chi phí API ngoài tăng ngoài dự kiến | Medium | Adapter, quota, budget alert, approval trước khi kích hoạt | Open |
| R-008 | Lộ secrets hoặc dữ liệu cá nhân | Critical | Environment secrets, least privilege, log redaction | Open |
| R-009 | Scope quá rộng khiến MVP không hoàn thành | High | Vertical slice, ưu tiên Trip/itinerary trước booking | Open |
| R-010 | Dữ liệu lịch trình AI ghi đè chỉnh sửa người dùng | Medium | Versioning, explicit replace confirmation | Open |

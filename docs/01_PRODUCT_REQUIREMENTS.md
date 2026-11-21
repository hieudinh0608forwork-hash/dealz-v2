# 01 – Product Requirements Document

## 1. Mục tiêu người dùng
Khách có thể bắt đầu từ nhu cầu du lịch, xây dựng hoặc chỉnh sửa hành trình khả thi, xem bản đồ và ngân sách, khám phá dịch vụ liên quan, gửi yêu cầu đặt và quản lý các dịch vụ trong cùng một chuyến đi. Đối tác có thể quản lý dịch vụ và phản hồi yêu cầu. Vận hành có thể kiểm duyệt và xử lý ngoại lệ.

## 2. Vai trò
- Guest: khám phá nội dung công khai.
- Traveler/Customer: quản lý hồ sơ, Trip, itinerary, budget, expense, share và booking của mình.
- Partner: quản lý hồ sơ tổ chức, listing, giá, availability và booking được phân quyền.
- Operations: hỗ trợ, kiểm duyệt, xử lý ngoại lệ theo capability.
- Administrator: cấu hình và quản trị toàn nền tảng.

Partner portal là giao diện riêng; không cấp quyền WordPress Administrator cho đối tác.

## 3. Hành trình khách hàng
### A. Khám phá
Trang chủ → khám phá điểm đến → trang chi tiết → dịch vụ liên quan. Nội dung ngoài Đà Nẵng – Hội An phải hiển thị cảnh báo ngoài khu thí điểm theo rulebook.

### B. Lập kế hoạch
Nhập điểm đến/ý định khám phá, nơi xuất phát nếu có, ngày, số người, ngân sách, sở thích, phương tiện và nhịp độ → tạo itinerary thủ công hoặc bằng AI khi chức năng AI đã được triển khai → kiểm tra lịch trình → chỉnh sửa → lưu.

### C. Xem lộ trình
Hiển thị theo thẻ từng ngày: tối đa 4 điểm dừng chính/ngày; cấu hình hiện tại là 3 điểm chính, bữa trưa/bữa tối là thẻ nhỏ. Có giờ đến, thời lượng gợi ý, góc chụp, đồ gợi ý, liên kết thuê đồ, chỉ đường và đoạn nối ước tính. Tuyệt đối không dùng dải vòng tròn đánh số.

### D. Ngân sách
Phân biệt estimate, supplier quote, confirmed booking amount và actual expense. Hiển thị giả định, nguồn, tiền/người và cảnh báo vượt ngân sách. Không dùng AI để kết luận khả năng chi trả.

### E. Dịch vụ và booking
Tìm dịch vụ theo loại, địa điểm, ngày và điều kiện. Xem fulfillment type, giá, phụ phí đã biết, cọc nếu được cấu hình, điều kiện và thời hạn báo giá. Checkout phải kiểm tra lại giá/availability phía server. Booking cần supplier confirmation không được hiển thị như đã xác nhận trước khi đối tác chấp nhận.

### F. Quản lý chuyến đi
Trip dashboard tổng hợp itinerary, từng booking, payment, expense và thông báo; các booking vẫn độc lập về trạng thái và điều kiện.

### G. Chia sẻ
Chủ Trip tạo và thu hồi liên kết chia sẻ. Quyền view/comment/edit phải được kiểm soát. Không lộ profile riêng tư, liên hệ, booking hoặc payment qua link công khai.

## 4. Functional requirements

| ID | Requirement | Priority | Acceptance |
|---|---|---|---|
| FR-001 | Register/login/logout/reset | P0 | Xác thực an toàn, lỗi có kiểm soát |
| FR-002 | Profile/preferences | P0 | Người dùng chỉ sửa hồ sơ được phép |
| FR-003 | Roles/capabilities | P0 | Guest, Traveler, Partner, Ops, Admin tách quyền |
| FR-004 | Destination/place catalogue | P0 | Có trạng thái, nguồn và dữ liệu công khai |
| FR-005 | Service catalogue | P0 | Listing có category, partner, giá basis, status |
| FR-006 | Trip CRUD | P0 | Owner tạo/xem/sửa/xóa Trip của mình |
| FR-007 | Manual itinerary editor | P0 | Thêm/sửa/xóa/sắp xếp/move item |
| FR-008 | AI itinerary generation | P1 | Schema-valid, preview, warnings, user acceptance |
| FR-009 | Schedule validation | P1 | Cảnh báo overlap, thời gian di chuyển không khả thi |
| FR-010 | Map and route display | P1 | Marker/list sync; route chỉ khi có dữ liệu routing |
| FR-011 | Budget and expenses | P1 | Tách estimate/quote/confirmed/actual |
| FR-012 | Trip share/collaboration | P1 | Quyền có thể thu hồi, không lộ dữ liệu riêng |
| FR-013 | Service search/filter | P0 | Lọc theo loại, vị trí, ngày khi có dữ liệu |
| FR-014 | Partner portal/listing | P1 | Partner chỉ quản lý tài nguyên được cấp |
| FR-015 | Quote/booking lifecycle | P1 | Fulfillment type và trạng thái đúng |
| FR-016 | Order aggregation | P1 | Một Order có thể có nhiều Booking độc lập |
| FR-017 | Payment adapter/webhook | P2 | Chỉ cập nhật từ sự kiện xác thực |
| FR-018 | Ledger/refund/commission/payout | P2 | Chỉ triển khai theo policy được phê duyệt |
| FR-019 | Notifications/support | P1 | Có log trạng thái gửi và xử lý lỗi |
| FR-020 | Admin/operations | P1 | Kiểm duyệt, hỗ trợ, audit |
| FR-021 | Analytics/reporting | P2 | Chỉ số tính từ dữ liệu thật, không giả lập |
| FR-022 | Itinerary save/share/print/PDF | P1 | Chỉ công bố nút khi tương tác đã hoạt động |

## 5. Danh mục dịch vụ định hướng
- Lưu trú.
- Thuê trang phục/đồ theo phong cách và điểm dừng.
- Nhiếp ảnh.
- Thuê phương tiện.
- Hoạt động/trải nghiệm/vé.
- Tour/hướng dẫn.
- Nhóm dịch vụ khác sau kiểm duyệt.

Danh mục này là phạm vi sản phẩm định hướng; không đồng nghĩa đã có nhà cung cấp hoặc inventory thực tế.

## 6. Non-functional requirements
Security: authentication, authorization, ownership, validation, escaping, prepared SQL, CSRF/nonce, rate limits.
Privacy: data minimization, public share redaction, safe logs.
Integrity: money in integer/decimal-safe representation, idempotency, immutable snapshots, audit.
Reliability: provider timeout/fallback, retry safe, no duplicate transaction.
Performance: pagination, indexes, caching with invalidation.
Accessibility: keyboard, focus, labels, contrast.
Responsive: mobile-first, tablet, desktop.
Maintainability: business logic in plugin, presentation in theme.
Observability: request IDs, safe logs, integration health.

## 7. Out of scope / not assumed
- Không xây inventory aggregator hàng không/khách sạn từ đầu.
- Không cam kết availability realtime khi không có provider/partner data.
- Không tự động mua/đặt từ bên thứ ba nếu chưa có xác nhận của người dùng.
- Không lending/credit scoring.
- Không settlement đa tiền tệ phức tạp trước khi VND flow ổn định.
- Không native mobile app trong giai đoạn đầu.
- Không microservices trước khi có nhu cầu đo được.

## 8. Presentation rule for unfinished capabilities
AI, payment và tiếng Anh là các hạng mục sản phẩm có thể nằm trong roadmap; tuy nhiên UI không được trình bày chúng như tính năng đang hoạt động cho đến khi code đã triển khai, cấu hình thật và kiểm thử. Nếu chưa có thì ẩn hoặc hiển thị trạng thái chưa hỗ trợ một cách rõ ràng, không dựng nút giả.

## 9. Acceptance gates
P0: login, role, destination/service catalogue, Trip CRUD, manual itinerary, ownership/security.
P1: AI planner, maps/routing, budget/expenses, sharing, partner portal, quote/booking.
P2: payment, financial ledger, refund/commission/payout, analytics.
Mỗi gate cần test và bằng chứng cụ thể trước khi đánh dấu đạt.

## 10. Open decisions
Provider AI/maps/geocoding/routing/weather/payment/email; hosting/runtime; service suppliers; exact commercial policies; booking availability model; domain and launch budget. Không tự suy diễn quyết định kinh doanh.

# 01 – Product Requirements

## 1. Người dùng và nhu cầu

### Traveler
- Khám phá điểm đến và dịch vụ.
- Tạo chuyến đi theo điểm đến, ngày đi, số người, sở thích và ngân sách.
- Nhận lịch trình gợi ý, chỉnh sửa và lưu.
- Xem điểm đến trên bản đồ, thứ tự hoạt động và thời gian di chuyển ước tính.
- Xem ngân sách dự kiến và các giả định cấu thành.
- Tìm dịch vụ phù hợp và gửi yêu cầu đặt.
- Theo dõi đơn hàng, từng booking và thông tin chuyến đi.

### Partner
- Quản lý hồ sơ đối tác và dịch vụ được cấp quyền.
- Cập nhật thông tin cung cấp, giá, lịch khả dụng và giới hạn phục vụ.
- Tiếp nhận, xác nhận/từ chối booking theo quy trình được duyệt.
- Theo dõi trạng thái giao dịch liên quan.

### Admin/Operations
- Quản lý người dùng, đối tác, điểm đến, danh mục và nội dung.
- Kiểm duyệt dịch vụ.
- Hỗ trợ booking, khiếu nại và vận hành.
- Theo dõi giao dịch, nhật ký và báo cáo.

## 2. Luồng sản phẩm chính
1. Người dùng khám phá hoặc tạo Trip.
2. Người dùng nhập thông tin chuyến đi.
3. AI tạo bản nháp itinerary có cấu trúc.
4. Backend kiểm tra cấu trúc và lưu phiên bản.
5. Người dùng chỉnh sửa, thêm/xóa/sắp xếp hoạt động.
6. Hệ thống tính ngân sách dự kiến từ dữ liệu/giả định có nguồn.
7. Người dùng khám phá dịch vụ.
8. Người dùng chọn dịch vụ và gửi yêu cầu checkout.
9. Backend kiểm tra giá, tồn khả dụng và chính sách tại thời điểm đặt.
10. Hệ thống tạo Order và các Booking liên quan theo trạng thái phù hợp.
11. Người dùng chủ động xác nhận thanh toán qua cổng được tích hợp.
12. Hệ thống cập nhật trạng thái dựa trên kết quả xác thực từ payment provider.
13. Người dùng theo dõi Trip, Order và từng Booking.

## 3. Phân loại yêu cầu
- Must: bắt buộc để MVP có thể sử dụng an toàn.
- Should: quan trọng nhưng có thể sau luồng cốt lõi.
- Could: cải thiện trải nghiệm.
- Later: ngoài MVP đầu tiên.

## 4. Yêu cầu chức năng baseline

| ID | Yêu cầu | Ưu tiên | Tiêu chí chấp nhận |
|---|---|---|---|
| FR-001 | Đăng ký/đăng nhập | Must | Người dùng xác thực được; lỗi được xử lý an toàn |
| FR-002 | Hồ sơ người dùng | Must | Người dùng xem/sửa dữ liệu được phép |
| FR-003 | Vai trò và quyền | Must | Traveler, Partner, Admin có quyền tách biệt |
| FR-004 | Danh mục điểm đến | Must | Admin quản lý; người dùng xem được nội dung đã xuất bản |
| FR-005 | Danh mục dịch vụ | Must | Dịch vụ có trạng thái xuất bản/ẩn và thông tin cơ bản |
| FR-006 | Tạo và quản lý Trip | Must | Người dùng tạo, xem, sửa Trip của mình |
| FR-007 | AI tạo itinerary | Should | Kết quả theo schema; lỗi AI không làm hỏng Trip |
| FR-008 | Chỉnh sửa itinerary | Must | Người dùng thêm/sửa/xóa/sắp xếp hoạt động |
| FR-009 | Bản đồ | Should | Hiển thị vị trí có tọa độ hợp lệ |
| FR-010 | Dự toán ngân sách | Must | Có tổng và các khoản cấu thành; nêu rõ ước tính |
| FR-011 | Tìm/lọc dịch vụ | Must | Lọc theo điểm đến, loại, ngày và điều kiện có dữ liệu |
| FR-012 | Khởi tạo Order/Booking | Later | Không tạo giao dịch không hợp lệ; tách vòng đời |
| FR-013 | Thanh toán | Later | Chỉ cập nhật theo callback/webhook xác thực |
| FR-014 | Dashboard chuyến đi | Must | Tập hợp Trip và thông tin liên quan của chủ sở hữu |
| FR-015 | Quản trị nội dung | Must | Admin quản lý điểm đến/dịch vụ |
| FR-016 | Quản lý dịch vụ đối tác | Should | Partner chỉ thao tác tài nguyên được cấp quyền |

## 5. Yêu cầu phi chức năng
- Security: xác thực, authorization, validation, escaping, rate limiting phù hợp.
- Privacy: chỉ thu thập dữ liệu cần thiết; hạn chế dữ liệu cá nhân trong log.
- Reliability: thao tác quan trọng có xử lý lỗi và khả năng retry an toàn.
- Integrity: ràng buộc dữ liệu và giao dịch nhất quán.
- Performance: phân trang, truy vấn có chỉ mục, tránh tải toàn bộ dữ liệu.
- Accessibility: điều khiển bàn phím, nhãn biểu mẫu, tương phản đủ.
- Responsive: hỗ trợ mobile, tablet và desktop.
- Maintainability: business logic trong plugin, UI trong theme.
- Observability: log có ngữ cảnh, không chứa secrets.

## 6. Ngoài phạm vi MVP ban đầu
- Microservices.
- Tự động đặt chỗ hoặc thanh toán bởi AI.
- Payout tự động nhiều đối tác trước khi có ledger/đối soát.
- Ứng dụng native iOS/Android.
- Dynamic pricing phức tạp.
- Loyalty/gamification nâng cao.

## 7. Câu hỏi còn mở
- Thị trường/địa bàn triển khai đầu tiên và danh mục dịch vụ ưu tiên cần được xác nhận từ tài liệu nghiệp vụ chi tiết.
- Quy định giá, tiền cọc, hủy/hoàn, hoa hồng và payout chưa được chốt trong baseline kỹ thuật.
- Nhà cung cấp AI, bản đồ, routing, thời tiết và thanh toán chưa được chọn.

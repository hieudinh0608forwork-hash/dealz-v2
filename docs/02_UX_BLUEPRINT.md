# 02 – UX Blueprint

## 1. Nguyên tắc
- Mobile-first, thao tác chính dễ thấy.
- Một màn hình tập trung một nhiệm vụ chính.
- Hiển thị rõ dữ liệu thật, dữ liệu ước tính và dữ liệu chưa có.
- Không giả lập tính năng chưa triển khai.
- Trạng thái loading, empty, error, success, disabled phải được thiết kế.
- Không hiển thị nút đặt/thanh toán như thể giao dịch đã được xác nhận.
- Tuân thủ đầy đủ `docs/11_DESIGN_RULES.md`; đây là nguồn chuẩn cho thiết kế giao diện.

## 2. Kiến trúc thông tin đề xuất

### Khu vực công khai
- Trang chủ
- Khám phá điểm đến
- Chi tiết điểm đến
- Danh sách dịch vụ
- Chi tiết dịch vụ
- Đăng nhập/đăng ký

### Khu vực Traveler
- Tổng quan
- Chuyến đi của tôi
- Tạo chuyến đi
- Trình lập lịch trình
- Chi tiết chuyến đi
- Khu vực thuê đồ
- Hồ sơ

### Khu vực Partner
- Tổng quan đối tác
- Hồ sơ đối tác
- Dịch vụ của tôi
- Lịch khả dụng
- Yêu cầu đặt dịch vụ
- Lịch sử booking

### Khu vực Admin
- Tổng quan vận hành
- Người dùng
- Đối tác
- Điểm đến
- Dịch vụ
- Booking/Order khi được triển khai
- Giao dịch và báo cáo khi có dữ liệu thật

## 3. Luồng tạo chuyến đi
1. Nhập điểm đến hoặc chọn khu vực.
2. Chọn ngày đi/ngày về, số người.
3. Nhập ngân sách, sở thích và nhịp độ mong muốn nếu các trường này đã được triển khai.
4. Xem tóm tắt đầu vào.
5. Tạo hoặc chỉnh sửa lịch trình theo năng lực hiện có của hệ thống.
6. Xem lộ trình dạng thẻ theo `docs/11_DESIGN_RULES.md`.
7. Lưu hành trình.
8. Chia sẻ, in hoặc xuất PDF nếu chức năng tương ứng đã được triển khai và kiểm thử.

## 4. Lộ trình dạng thẻ – tiêu chuẩn bắt buộc
- Tối đa 4 điểm dừng chính/ngày; phiên bản hiện tại dựng 3 điểm chính, bữa trưa và bữa tối là thẻ nhỏ.
- Thẻ điểm dừng có ảnh tỷ lệ 16:10, giờ đến, thời lượng gợi ý, góc chụp (gợi ý chung nếu chưa có dữ liệu), đồ gợi ý, "Thuê đồ cho điểm này", "Chỉ đường" (Google Maps).
- Liên kết thuê đồ mở trang Thuê đồ đã lọc theo phong cách và có "Quay lại [điểm dừng]".
- Đoạn nối dùng đường nét liền, biểu tượng xe máy và thời gian/khoảng cách ước tính. Chỉ dùng số liệu có căn cứ.
- Dải đầu ngày hiển thị tên điểm dừng, cuộn ngang; hỗ trợ Lưu, Chia sẻ, In hoặc PDF khi đã triển khai.
- Điểm đến ngoài Đà Nẵng – Hội An có cảnh báo "ngoài khu thí điểm".
- Cấm dải vòng tròn đánh số.

## 5. Thành phần màn hình
- Tiêu đề chuyến đi và thời gian.
- Dải tên điểm dừng đầu ngày, cuộn ngang.
- Các thẻ điểm dừng theo thứ tự.
- Thời gian đến và thời lượng gợi ý.
- Thông tin di chuyển chỉ khi có dữ liệu; gắn nhãn ước tính.
- Nút chỉ đường mở Google Maps.
- Liên kết thuê đồ theo phong cách của điểm dừng.
- Khối "Tiếp theo" ở cuối mỗi trang Khách.
- Bản đồ chỉ hiển thị khi đã có dữ liệu vị trí hợp lệ.

## 6. Điều hướng và trạng thái
- Mỗi màn có địa chỉ riêng để Back hoạt động.
- Thanh điều hướng dưới điện thoại tối đa 5 mục, có biểu tượng và chữ; Hành trình có số đếm.
- Thêm vào hành trình hiển thị thông báo và nút "Xem hành trình".
- Trạng thái trống luôn nêu việc nên làm và có một nút hành động.
- Loading: thể hiện tiến trình thật.
- Error: thông báo dễ hiểu, giữ dữ liệu đã nhập.
- Success: chỉ xác nhận hành động thực sự hoàn tất.
- Disabled: giải thích điều kiện chưa đáp ứng.

## 7. Tính năng chưa được phép trình bày như đang hoạt động
Theo quy tắc thiết kế hiện hành, không hiển thị như tính năng sẵn sàng các chức năng thanh toán, tiếng Anh hoặc AI khi chúng chưa được triển khai và kiểm thử. Không hiển thị liên hệ shop trước khi DealZ xác nhận. Không tạo số liệu giả trong cổng đối tác.

## 8. Wireframe và acceptance
Wireframe và giao diện chỉ được coi là hoàn thành khi đối chiếu toàn bộ `docs/11_DESIGN_RULES.md`, kiểm tra trên thiết bị phù hợp và xác minh các tương tác được mô tả thực sự hoạt động.

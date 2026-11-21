# 02 – UX Blueprint

## 1. Nguyên tắc
- Mobile-first, thao tác chính dễ thấy.
- Một màn hình tập trung một nhiệm vụ chính.
- AI tạo bản nháp có thể sửa, không khóa người dùng vào kết quả.
- Hiển thị rõ dữ liệu thực, dữ liệu ước tính và dữ liệu chưa có.
- Trạng thái loading, empty, error, success phải được thiết kế.
- Không hiển thị nút đặt/ thanh toán như thể giao dịch đã được xác nhận.

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
- Dịch vụ đã chọn/đơn hàng (khi booking được triển khai)
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
- Booking/Order
- Giao dịch và báo cáo

## 3. Luồng tạo chuyến đi
1. Nhập điểm đến hoặc chọn khu vực.
2. Chọn ngày đi/ngày về, số người.
3. Nhập ngân sách, sở thích và nhịp độ mong muốn (các trường có thể tùy chọn).
4. Xem tóm tắt đầu vào.
5. Yêu cầu AI tạo bản nháp.
6. Xem lịch trình theo ngày và bản đồ.
7. Chỉnh sửa hoạt động.
8. Lưu Trip.

## 4. Thành phần màn hình itinerary
- Tiêu đề chuyến đi và thời gian.
- Tabs/ngày.
- Danh sách hoạt động theo thứ tự.
- Thời gian dự kiến, địa chỉ, tọa độ nếu có.
- Khoảng di chuyển và phương thức nếu có dữ liệu routing.
- Ước tính chi phí và nguồn/giả định.
- Bản đồ.
- Nút chỉnh sửa, thêm hoạt động, tạo lại gợi ý.

## 5. Trạng thái giao diện bắt buộc
- Loading: tiến trình rõ ràng, không giả vờ có kết quả.
- Empty: hướng dẫn hành động tiếp theo.
- Error: thông báo dễ hiểu, giữ dữ liệu người dùng đã nhập.
- Success: xác nhận đúng hành động đã hoàn thành.
- Disabled: giải thích điều kiện chưa đáp ứng.
- Stale data: thông báo giá/lịch khả dụng cần kiểm tra lại trước khi đặt.

## 6. Design system baseline
- Primary: #FD550A.
- Deep green: #015D47.
- Lime: #E0F354.
- Typography đậm cho tiêu đề, dễ đọc cho nội dung.
- Bo góc thẻ vừa phải; phân cấp rõ; tránh trang trí dày đặc.
- Component cần có trạng thái focus, hover, disabled, loading và error.
- Không dùng màu sắc làm tín hiệu duy nhất cho trạng thái.

## 7. Wireframe và acceptance
Wireframe chi tiết sẽ được lập sau khi chốt sitemap MVP và có kiểm tra các luồng thực tế. Không coi tài liệu này là bản thiết kế giao diện hoàn chỉnh.

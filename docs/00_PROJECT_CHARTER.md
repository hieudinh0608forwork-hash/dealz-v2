# 00 – Project Charter

## 1. Tầm nhìn
DealZ là nền tảng du lịch tích hợp AI, hỗ trợ xuyên suốt hành trình: Khám phá → Lên kế hoạch → Tùy chỉnh lịch trình → Tính ngân sách → Đặt dịch vụ → Thanh toán → Quản lý chuyến đi.

## 2. Các bên tham gia
- Travelers: tìm hiểu điểm đến, lập kế hoạch và đặt dịch vụ.
- Partners: cung cấp dịch vụ du lịch và quản lý nội dung/khả năng cung ứng.
- DealZ Platform: kết nối, hỗ trợ AI, điều phối đặt dịch vụ và quản lý giao dịch.
- Administrators/Operations: kiểm duyệt, hỗ trợ, vận hành và đối soát.

## 3. Giá trị cốt lõi
- Lịch trình cá nhân hóa có AI hỗ trợ.
- Bản đồ, điểm đến, tuyến đường.
- Dự toán ngân sách.
- Khám phá và đặt nhiều dịch vụ.
- Quản lý lịch trình và booking tập trung.

## 4. Phạm vi định hướng
Mục tiêu dài hạn là nền tảng đầy đủ. MVP phải được chia lát theo khả năng vận hành và kiểm thử; không đồng nghĩa phải triển khai toàn bộ thanh toán, payout và tích hợp ngoài ngay từ đầu.

## 5. Nguyên tắc sản phẩm
1. AI đề xuất, người dùng quyết định.
2. Trip, Order và Booking độc lập về dữ liệu và vòng đời.
3. Một Order có thể gồm nhiều Booking.
4. Mỗi Booking có trạng thái riêng theo dịch vụ/đối tác.
5. Backend là nguồn dữ liệu chính thức.
6. Phân quyền theo vai trò và quyền sở hữu tài nguyên.
7. Tài chính phải có lịch sử bất biến/khả năng truy vết và đối soát.
8. Không dùng dữ liệu mẫu như dữ liệu thật.

## 6. Nhận diện thương hiệu
- Orange: #FD550A – CTA/điểm nhấn chính.
- Deep Green: #015D47 – màu nhận diện/nền.
- Lime: #E0F354 – điểm nhấn phụ.
Phong cách: typography đậm, tương phản cao, hình ảnh du lịch biên tập, giao diện tối giản hiện đại, thẻ bo tròn vừa phải.

## 7. Ràng buộc và giới hạn
- Dự án V2 mới, độc lập với V1.
- Không nhập theme, plugin, database hoặc quy trình V1.
- Công nghệ hiện là định hướng, cần xác minh tương thích và chi phí.
- Chưa xác minh hosting, domain, phiên bản PHP/WordPress/MySQL hay dịch vụ ngoài.
- Không phát sinh chi phí hoặc triển khai production khi chưa được chủ dự án duyệt.

## 8. Tiêu chí thành công
- Luồng người dùng cốt lõi chạy được từ tạo chuyến đi đến lưu lịch trình.
- Dữ liệu có thể truy xuất chính xác và phân quyền đúng.
- AI trả dữ liệu cấu trúc có kiểm tra, có thể chỉnh sửa; không tự giao dịch.
- Booking/Order có trạng thái rõ ràng, xử lý lỗi an toàn.
- Có kiểm thử tự động và bằng chứng kiểm thử thủ công.
- Có tài liệu vận hành và hướng dẫn cài đặt.

## 9. Trạng thái
Baseline tài liệu được khởi tạo ngày 2026-11-22. Các giả định chưa xác minh được ghi trong Requirements Register và Risk Register.

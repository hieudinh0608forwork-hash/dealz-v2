# DealZ V2

Nền tảng du lịch tích hợp AI, kết nối khách du lịch với điểm đến và nhà cung cấp dịch vụ.

## Mục tiêu
Hỗ trợ người dùng khám phá, lập lịch trình, dự toán ngân sách, tìm và đặt dịch vụ, thanh toán và quản lý chuyến đi trong một trải nghiệm thống nhất.

## Trạng thái
- Giai đoạn: Phase 0 – Discovery & Audit
- Repository: `hieudinh0608forwork-hash/dealz-v2`
- Branch chính: `main`
- Kiến trúc định hướng: modular monolith trên WordPress, custom plugin `dealz-core`, custom theme `dealz-theme`.
- Chưa có mã nguồn ứng dụng hoặc môi trường WordPress được xác minh.

## Tài liệu
Xem [docs/](docs/) để đọc Project Charter, yêu cầu sản phẩm, kiến trúc, dữ liệu, API, quy tắc nghiệp vụ, rủi ro, backlog và kế hoạch kiểm thử.

## Nguyên tắc
- V2 là dự án độc lập; không nhập mã nguồn, database, theme hoặc quy trình V1.
- Backend là nguồn dữ liệu chính thức.
- AI chỉ đề xuất; người dùng phải chủ động xác nhận đặt dịch vụ và thanh toán.
- Không đánh dấu Tested/Verified/Deployed nếu chưa có bằng chứng.
- Không phát sinh dịch vụ trả phí hoặc triển khai production khi chưa được người dùng duyệt.

## Bắt đầu
Các tài liệu Phase 0 hiện là baseline cần được xác minh và cập nhật khi có bằng chứng mới. Chưa có hướng dẫn cài đặt ứng dụng vì môi trường triển khai chưa được kiểm kê.

# 00 – Project Charter

## 1. Identity and vision
**DealZ V2** là dự án mới, độc lập với DealZ V1: nền tảng du lịch tích hợp, kết nối khách du lịch, nhà cung cấp dịch vụ và nền tảng công nghệ. Hành trình sản phẩm định hướng: Khám phá → Lên kế hoạch → Tùy chỉnh lịch trình → Tính ngân sách → Tìm/đặt dịch vụ → Thanh toán → Quản lý chuyến đi.

DealZ không chỉ là website bán dịch vụ. Giá trị cốt lõi là kết hợp kế hoạch chuyến đi khả thi, dữ liệu điểm đến, lộ trình, ngân sách và các dịch vụ liên quan trong một trải nghiệm.

## 2. Thành phần hệ sinh thái
- **Travelers:** khách du lịch cá nhân, cặp đôi, nhóm bạn, gia đình.
- **Partners:** nhà cung cấp dịch vụ du lịch, quản lý listing và khả năng cung ứng.
- **DealZ Platform:** kết nối, điều phối trải nghiệm, quản lý thông tin và giao dịch.
- **Operations/Admin:** kiểm duyệt, hỗ trợ, xử lý ngoại lệ, tài chính và vận hành.

## 3. Giá trị sản phẩm
- Lập lịch trình theo ngày, có thể chỉnh sửa.
- Bản đồ, địa điểm và tuyến đường có căn cứ.
- Ngân sách minh bạch: dự kiến, báo giá/xác nhận và chi phí thực tế.
- Tìm dịch vụ phù hợp với lịch trình, địa điểm và thời gian.
- Quản lý nhiều booking độc lập trong cùng một chuyến đi.
- Kết nối khách hàng với nhà cung cấp, hỗ trợ đối tác quản lý dịch vụ.

## 4. Phạm vi sản phẩm
Full-scope product direction gồm:
- Tài khoản, hồ sơ và phân quyền.
- Điểm đến/địa điểm và nội dung khám phá.
- Trip, itinerary editor, chia sẻ/cộng tác.
- AI travel planner và kiểm tra lịch trình.
- Bản đồ, geocoding, routing.
- Budget, expense và dự toán theo người.
- Service marketplace: lưu trú, thuê trang phục/đồ, nhiếp ảnh, phương tiện, hoạt động/trải nghiệm, tour/hướng dẫn và nhóm dịch vụ được duyệt.
- Partner portal, listing, giá, availability và xử lý yêu cầu.
- Order, booking, payment adapter, ledger, refund/commission/payout sau khi chính sách được duyệt.
- Notifications, support, admin, analytics và audit.

Phạm vi triển khai được chia thành các vertical slice có thể chạy và kiểm thử; không tuyên bố các tích hợp ngoài đang hoạt động khi chưa có provider, credentials và kiểm chứng.

## 5. Thị trường và khu thí điểm
Thiết kế sản phẩm có cảnh báo "ngoài khu thí điểm" khi điểm đến nằm ngoài Đà Nẵng – Hội An. Đây là phạm vi thí điểm được nêu trong quy tắc thiết kế. Không giả định dịch vụ/nguồn cung thật đã có ngoài khu vực này.

## 6. Nguyên tắc sản phẩm bắt buộc
1. Người dùng quyết định; AI chỉ đề xuất.
2. Backend là nguồn dữ liệu chính thức.
3. Trip, Order và Booking là các thực thể riêng.
4. Một Order có thể gồm nhiều Booking độc lập.
5. Booking có trạng thái và điều kiện riêng theo dịch vụ/đối tác.
6. Phân quyền theo vai trò và quyền sở hữu.
7. Giá, availability, thời gian tuyến và thông tin địa điểm phải có nguồn hoặc nhãn ước tính.
8. Không dùng dữ liệu giả làm dữ liệu đối tác/production.
9. Không tự ý thay đổi chính sách cọc, hủy, hoàn, hoa hồng hoặc payout.
10. Không trình bày tính năng chưa triển khai như đang hoạt động.

## 7. Brand
Các màu nhận diện nền tảng: Orange #FD550A, Deep Green #015D47, Lime #E0F354. Quy tắc font, màu nút, card, input, navigation và itinerary được quy định chi tiết tại [11_DESIGN_RULES.md](11_DESIGN_RULES.md), là nguồn chuẩn giao diện.

## 8. Kiến trúc định hướng
Fresh WordPress installation; custom plugin `dealz-core` chứa nghiệp vụ; custom theme `dealz-theme` chỉ trình bày; REST API; MySQL; modular monolith. Không chỉnh sửa WordPress core. Không cài theme V1 nguyên trạng hoặc tái sử dụng code/database/plugin V1.

## 9. Quyền tự chủ và ranh giới
Người điều phối được chủ động quyết định kỹ thuật thông thường, tạo tài liệu, viết code, kiểm thử và tổ chức backlog. Phải dừng trước khi phát sinh chi phí, kích hoạt dịch vụ có hệ quả tài chính, thay đổi chính sách thương mại/pháp lý, xóa dữ liệu production hoặc deploy production có rủi ro đáng kể.

## 10. Definition of success
- Hành trình tạo, lưu, mở lại và chỉnh sửa Trip hoạt động trên WordPress thật.
- Itinerary có dữ liệu cấu trúc, chỉnh sửa được, không bị AI ghi đè âm thầm.
- Bản đồ và tuyến đường phân biệt dữ liệu thật/ước tính/không có.
- Budget tách estimate, quote/confirmed và actual.
- Marketplace và booking phản ánh đúng fulfillment type, supplier confirmation và payment state.
- Partner chỉ truy cập dữ liệu của mình.
- Có test, bằng chứng, hướng dẫn setup, backup/rollback và tài liệu vận hành.
- Không còn lỗi nghiêm trọng về quyền truy cập, dữ liệu hoặc giao dịch ở release gate.

## 11. Trạng thái
Ngày 2026-11-22: repository đã có Project Knowledge Pack và các tài liệu thiết kế được cập nhật theo quy tắc 20/11/2026. Chưa có code ứng dụng, môi trường WordPress hoặc deployment được xác minh.

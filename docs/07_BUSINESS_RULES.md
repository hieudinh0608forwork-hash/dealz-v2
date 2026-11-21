# 07 – Business Rules Baseline

## 1. Quy tắc đã được xác định
- AI chỉ hỗ trợ đề xuất; không tự xác nhận đặt chỗ hoặc thanh toán.
- Trip, Order và Booking là các thực thể khác nhau.
- Một Order có thể chứa nhiều Booking.
- Booking có trạng thái riêng theo dịch vụ/đối tác.
- Backend là nguồn dữ liệu chính thức.
- Không hard-code chính sách kinh doanh trong giao diện.

## 2. Quy tắc cần áp dụng khi triển khai
- Giá hiển thị có thể thay đổi; backend phải xác minh lại giá tại thời điểm checkout.
- Khả dụng phải được kiểm tra lại khi tạo booking.
- Người dùng phải nhìn thấy thông tin dịch vụ, số lượng, giá, phí và điều kiện trước khi xác nhận.
- Mọi thay đổi trạng thái giao dịch phải có actor/source, timestamp và lý do.
- Partner chỉ xem và xử lý booking thuộc phạm vi được phân quyền.
- Admin action quan trọng cần audit trail.

## 3. Các chính sách chưa được phê duyệt
Không tự đặt giá trị hoặc quy tắc mặc định cho:
- Tiền cọc và tài sản bảo đảm.
- Hủy booking và hoàn tiền.
- Phí nền tảng/hoa hồng.
- Payout và lịch thanh toán đối tác.
- Phí dịch vụ, thuế và phụ phí.
- Thời hạn giữ chỗ.
- Xử lý no-show, tranh chấp và bồi thường.
- Điều kiện xác nhận booking theo từng loại dịch vụ.

## 4. Quy tắc tài chính
- Tổng tiền phải được tính phía server từ snapshot giá và các khoản đã được phê duyệt.
- Mọi khoản tiền có currency.
- Payment event không đồng nghĩa ledger entry nếu chưa qua quy tắc hạch toán.
- Không sửa/xóa lịch sử tài chính đã ghi; dùng bút toán điều chỉnh có tham chiếu.
- Reconciliation cần so khớp nội bộ với báo cáo provider.

## 5. Quy tắc tồn khả dụng
- Không xác nhận vượt capacity.
- Cần cơ chế khóa/transaction khi giữ hoặc xác nhận chỗ.
- Thời gian giữ chỗ, giải phóng và timeout phải được phê duyệt theo từng mô hình dịch vụ.

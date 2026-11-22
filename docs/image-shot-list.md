# DealZ – Danh sách ảnh cần chụp và kiểm duyệt

Phạm vi: chỉ ảnh thật do đội dự án tự chụp hoặc ảnh có giấy phép cho phép sử dụng. Không dùng ảnh AI/stock để giả làm ảnh địa điểm đã xác minh.

## Quy cách chung
- Ảnh thẻ điểm dừng: tỷ lệ 16:10, tối thiểu 1600×1000 px.
- Ảnh bìa/ảnh dọc trang: tỷ lệ 4:5, tối thiểu 1600×2000 px.
- Chụp bản gốc độ phân giải cao, giữ metadata gốc khi có thể; bản web được nén nhưng không cắt mất nội dung quan trọng.
- Không chèn logo đối tác, đánh giá, giá, bảng hiệu giả hoặc yếu tố khiến người xem hiểu nhầm có quan hệ thương mại.
- Mỗi ảnh phải có alt text mô tả đúng chủ thể trong khung hình và dòng ghi công theo hồ sơ media credits.
- Chỉ gắn ảnh vào điểm dừng khi người duyệt xác minh được ảnh khớp địa điểm; nếu chưa có ảnh phù hợp, tiếp tục dùng ImageSlot trống.

## Shot list theo loại item hiện có trong dữ liệu
Mã loại lấy từ REST item_type: stop, lunch, dinner. Không tự tạo taxonomy sản phẩm/địa điểm mới trong lúc chụp.

| Loại | Ảnh 16:10 | Ảnh 4:5 | Góc chụp / chủ thể |
|---|---|---|---|
| stop – Điểm dừng chính | Bối cảnh thật của địa điểm; toàn cảnh dễ nhận diện; tránh người lạ làm chủ thể chính | Khung dọc thể hiện chiều sâu hoặc chi tiết kiến trúc/cảnh quan đặc trưng | Một ảnh toàn cảnh ngang, một ảnh dọc có tiền cảnh–trung cảnh–hậu cảnh; không thêm nhãn địa danh lên ảnh |
| lunch – Bữa trưa | Không chụp món ăn/nhà hàng nếu chưa xác minh được đúng địa điểm và quyền sử dụng | Chỉ chụp món ăn hoặc không gian khi có sự đồng ý của chủ thể/đơn vị sở hữu | Ưu tiên ánh sáng tự nhiên, món ăn thực tế; không dàn dựng thành thực đơn hoặc giá bán |
| dinner – Bữa tối | Không chụp món ăn/nhà hàng nếu chưa xác minh được đúng địa điểm và quyền sử dụng | Chỉ chụp món ăn hoặc không gian khi có sự đồng ý của chủ thể/đơn vị sở hữu | Ghi đúng điều kiện ánh sáng; không tăng màu làm sai hiện trạng, không hàm ý cửa hàng đối tác |

## Quy trình duyệt
1. Ghi mã điểm dừng và người chụp; không dùng tên địa điểm làm bằng chứng duy nhất.
2. Kiểm tra quyền chụp, quyền sử dụng, người xuất hiện có cần đồng ý hay không.
3. Kiểm tra ảnh có đúng địa điểm và đúng nội dung mô tả.
4. Điền hồ sơ trong media-credits.json theo mẫu.
5. Người duyệt xác nhận trước khi ảnh được đưa lên site.
6. Nếu thiếu bất kỳ bằng chứng nào, không xuất bản ảnh; dùng ImageSlot trống.

## Không được làm
- Không dùng ảnh lấy từ Google Maps, mạng xã hội hoặc website của bên thứ ba nếu chưa có giấy phép phù hợp.
- Không tự suy ra giấy phép từ việc ảnh được công khai trên internet.
- Không gán ảnh chung cho một địa điểm cụ thể.
- Không dùng ảnh của bữa ăn để ám chỉ có đối tác nhà hàng hoặc giá đã xác minh.

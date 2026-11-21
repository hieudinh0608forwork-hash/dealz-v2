# 05 – Database Schema Baseline

## 1. Nguyên tắc
- Mỗi thực thể có ID ổn định, timestamps và trạng thái phù hợp.
- Dữ liệu quan hệ có foreign key logic và index; nếu dùng custom tables phải đánh giá hỗ trợ ràng buộc của MySQL.
- Không lưu dữ liệu giao dịch quan trọng chỉ trong post meta không có cấu trúc.
- Tiền lưu bằng integer đơn vị nhỏ nhất hoặc DECIMAL phù hợp; không dùng floating point.
- Lưu currency rõ ràng.
- Thời gian lưu theo UTC; hiển thị theo timezone phù hợp.

## 2. Thực thể đề xuất

### users
Có thể dùng WordPress users làm identity; profile mở rộng ở bảng riêng khi cần.

### partners
- id, owner_user_id, display_name, status, contact fields, created_at, updated_at.

### destinations
- id, slug, name, description, region, country_code, latitude, longitude, status.

### services
- id, partner_id, destination_id, category_id, title, description, price_amount, currency, pricing_unit, status, created_at, updated_at.

### service_availability
- id, service_id, start_at, end_at, capacity, remaining_capacity, status, version.

### trips
- id, traveler_user_id, title, destination_id, start_date, end_date, traveler_count, budget_amount, currency, status.

### itineraries
- id, trip_id, version, source (manual/ai), status, input_snapshot, created_at.

### itinerary_items
- id, itinerary_id, day_number, position, title, description, start_at, end_at, destination_name, address, latitude, longitude, estimated_cost, currency, metadata.

### orders
- id, traveler_user_id, trip_id nullable, order_number, subtotal, fees, discount, total, currency, status, idempotency_key, created_at.

### bookings
- id, order_id, service_id, partner_id, quantity, unit_price, total_amount, currency, status, provider_reference, service_snapshot, created_at.

### payments
- id, order_id, provider, provider_reference, amount, currency, status, idempotency_key, paid_at, raw_event_reference.

### ledger_entries
- id, transaction_group_id, account_code, debit_amount, credit_amount, currency, source_type, source_id, created_at.

## 3. Quan hệ
- User 1:N Trips.
- Partner 1:N Services.
- Trip 1:N Itineraries.
- Itinerary 1:N ItineraryItems.
- Order 1:N Bookings.
- Order 1:N Payments.
- Booking thuộc một Partner và Service tại thời điểm đặt.
- Trip không phải Order; Order không phải Booking.

## 4. Ràng buộc và index cần đánh giá
- Unique: destination.slug, order.order_number, provider + provider_reference khi provider đảm bảo duy nhất, idempotency_key theo phạm vi.
- Index: owner/status/date; service/destination/status; booking partner/status; payment order/status.
- Kiểm tra availability và tạo booking phải có transaction/locking phù hợp để tránh overbooking.

## 5. Lưu ý
Đây là logical schema, chưa phải migration SQL cuối cùng. Cần chốt chính sách booking, giá, tồn khả dụng, hủy/hoàn và lựa chọn lưu trữ trước khi triển khai vật lý.

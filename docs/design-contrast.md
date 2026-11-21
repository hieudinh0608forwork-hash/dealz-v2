# DealZ – Ma trận tương phản WCAG 2.2 AA

Ngày đo: 22/11/2026  
Nguồn token: `wp-content/themes/dealz-theme/assets/css/tokens.css`.  
Phương pháp: WCAG relative luminance, sRGB linearization, ratio (L1 + 0.05) / (L2 + 0.05). Kết quả hiển thị làm tròn; CI tính từ token chưa làm tròn.

## Ma trận các cặp đang dùng

### Chữ thường (ngưỡng 4,5:1)

| Token | Trắng #FFFFFF | Kem #FBF7EE | Mint-50 #F2F7F4 | Mint-100 #E4EFE9 | Lime-50 #F7FAD8 | Orange-50 #FFEEE6 | Kết quả |
|---|---:|---:|---:|---:|---:|---:|---|
| Ink #0E2A22 | 15,30 | 14,31 | 14,12 | 12,98 | 14,29 | 13,57 | Đạt tất cả |
| Ink-2 #4C635A | 6,48 | 6,06 | 5,98 | 5,50 | 6,06 | 5,75 | Đạt tất cả |
| Label #31483F | 9,86 | 9,22 | 9,10 | 8,37 | 9,21 | 8,75 | Đạt tất cả |

### Placeholder

| Cặp | Tỷ lệ | Ngưỡng | Kết quả |
|---|---:|---:|---|
| #5F776D trên trắng #FFFFFF | 4,83:1 | 4,5:1 | Đạt |

### Chữ cam #C43A00

| Nền | Tỷ lệ | Ngưỡng | Kết quả |
|---|---:|---:|---|
| Trắng #FFFFFF | 5,31:1 | 4,5:1 | Đạt |
| Kem #FBF7EE | 4,97:1 | 4,5:1 | Đạt |
| Mint-50 #F2F7F4 | 4,90:1 | 4,5:1 | Đạt |
| Lime-50 #F7FAD8 | 4,96:1 | 4,5:1 | Đạt |
| Orange-50 #FFEEE6 | 4,71:1 | 4,5:1 | Đạt |
| Mint-100 #E4EFE9 | 4,51:1 | 3,0:1 (chỉ chữ lớn ≥24px hoặc ≥19px đậm) | Đạt cho chữ lớn; không dùng chữ thường |

### Chữ trắng/kem trên nền tối

| Cặp | Tỷ lệ | Kết quả |
|---|---:|---|
| Trắng #FFFFFF trên forest #015D47 | 7,90:1 | Đạt |
| Kem #FBF7EE trên forest #015D47 | 7,39:1 | Đạt |
| Trắng #FFFFFF trên nút #D63F00 | 4,59:1 | Đạt; nút dùng chữ trắng |
| Kem #FBF7EE trên nút #D63F00 | 4,30:1 | Không đạt; loại khỏi cách dùng, không dùng chữ kem trên nút chính |

### Focus

| Cặp | Tỷ lệ | Ngưỡng | Kết quả |
|---|---:|---:|---|
| Outline đặc #D63F00 trên trắng | 4,59:1 | 3,0:1 | Đạt |
| Outline đặc #D63F00 trên kem | 4,30:1 | 3,0:1 | Đạt |
| Outline đặc #D63F00 trên mint-50 | 4,24:1 | 3,0:1 | Đạt |
| Outline đặc #D63F00 trên mint-100 | 3,90:1 | 3,0:1 | Đạt |
| Outline đặc #D63F00 trên lime-50 | 4,29:1 | 3,0:1 | Đạt |
| Outline đặc #D63F00 trên orange-50 | 4,08:1 | 3,0:1 | Đạt |
| Vòng lime #E0F354 trên forest #015D47 | 6,44:1 | 3,0:1 | Đạt |

### Viền input và focus input

| Cặp | Tỷ lệ | Ngưỡng | Kết quả |
|---|---:|---:|---|
| Viền #7C948A với nền trong input trắng | 3,25:1 | 3,0:1 | Đạt |
| Viền #7C948A với nền xung quanh trắng | 3,25:1 | 3,0:1 | Đạt |
| Viền #7C948A với nền xung quanh kem | 3,04:1 | 3,0:1 | Đạt |
| Viền #7C948A với nền xung quanh mint-50 | 3,00095:1 | 3,0:1 | Đạt sát ngưỡng |
| Viền focus #FD550A với nền input trắng | 3,24:1 | 3,0:1 | Đạt |
| Quầng focus 4px rgba(253,85,10,0.16), composite trên trắng thành #FFE4D8 | 1,21:1 | Trang trí, không phải chỉ báo độc lập | Không dùng làm chỉ báo duy nhất; viền đặc #FD550A đạt 3,24:1 |

#7C948A đạt ngưỡng trên cả ba nền ngoài được cho phép; không cần đổi sang #6F887D. Ô input luôn có nền trắng. Viền thẻ #CFE0D8 không đổi.

## Các cặp loại khỏi ma trận và lý do

- Placeholder trên kem/mint/lime/orange: không sử dụng; input bắt buộc nền trắng.
- Chữ cam trên mint-100 ở cỡ chữ thường: không đạt ngưỡng chữ thường; chỉ cho phép chữ lớn, ratio 4,51:1.
- Chữ kem trên nút #D63F00: ratio 4,30:1; loại khỏi UI, nút chính dùng chữ trắng.
- Viền thẻ #CFE0D8: viền trang trí, không dùng làm ranh giới duy nhất của control tương tác; không áp ngưỡng 3:1.
- Quầng focus alpha: không được tính là chỉ báo focus duy nhất; outline/viền focus đặc mới là chỉ báo đạt ngưỡng.
- Placeholder/viền input trên forest: không có input trên forest trong layout hiện tại; không thuộc cặp đang dùng.

## Kết luận token

- Placeholder đổi từ #7C948A sang #5F776D theo quyết định chủ dự án.
- Viền input tách token #7C948A; viền thẻ vẫn #CFE0D8.
- Chữ cam ngữ nghĩa dùng #C43A00; nền nút chính vẫn #D63F00.
- Outline focus ngoài dùng #D63F00 đặc; trên nền forest dùng lime.
- Không đổi token viền input vì #7C948A đạt 3,00095:1 trên mint-50, thấp nhất trong các nền ngoài được phép.

## Tự động hóa

CI chạy `scripts/check-design-contrast.php` để đọc token thực tế và kiểm tra từng cặp/ngưỡng trong ma trận. CI chạy `scripts/check-css-tokens.php` để cấm mã hex ngoài `assets/css/tokens.css` trong mã nguồn theme. Ma trận không áp dụng cho các cặp không dùng trong UI; ví dụ placeholder trên nền ngoài trắng, chữ kem trên nút cam, input trên forest và viền thẻ mang tính trang trí.

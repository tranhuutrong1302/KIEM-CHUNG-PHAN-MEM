# API Integration Test - Royal Bid

## 1. Thông tin chung

**Dự án:** Royal Bid - Hệ thống đấu giá
**Loại kiểm thử:** Integration Test
**Công cụ kiểm thử:** Postman, XAMPP, phpMyAdmin, Jira
**Môi trường:** Localhost
**Backend:** PHP + MySQL
**Người thực hiện:** Nhóm kiểm thử Integration Test

---

## 2. Mục tiêu kiểm thử

Kiểm thử sự tích hợp giữa các thành phần chính của hệ thống:

* Client/Postman
* REST API PHP
* Session đăng nhập
* Database MySQL
* Luồng xử lý User và Admin

Mục tiêu là xác nhận các chức năng hoạt động đúng theo luồng nghiệp vụ và phát hiện lỗi phát sinh khi các thành phần tương tác với nhau.

---

## 3. Phạm vi kiểm thử

Các nhóm chức năng được kiểm thử:

* User đã đăng nhập
* Đặt giá sản phẩm
* Xác nhận chuyển khoản
* Admin Dashboard
* Admin duyệt/từ chối đơn thanh toán
* Logout
* Admin thêm/xóa sản phẩm

---

## 4. Kết quả kiểm thử tổng hợp

| Test Case ID | Chức năng                        | Kết quả lý tưởng                                   | Kết quả thực tế                                  | Status | Jira     |
| ------------ | -------------------------------- | -------------------------------------------------- | ------------------------------------------------ | ------ | -------- |
| IT05.1       | GET my_bids có session           | Trả JSON danh sách đấu giá của user                | Trả JSON success=true                            | PASS   | -        |
| IT05.2       | GET trang chủ lấy product_id     | Hiển thị sản phẩm còn hạn                          | Hiển thị đúng                                    | PASS   | -        |
| IT05.3       | POST đặt giá hợp lệ              | Đặt giá thành công                                 | Fatal Error validateBid()                        | FAIL   | SCRUM-49 |
| IT05.4       | POST đặt giá quá thấp            | Từ chối giá không hợp lệ và hiển thị thông báo lỗi | Không hiển thị thông báo lỗi đúng theo test case | FAIL   | SCRUM-52 |
| IT05.5       | GET my_bids sau khi đặt giá      | Hiển thị lịch sử đấu giá                           | Không hiển thị dữ liệu đấu giá theo mong đợi     | FAIL   | SCRUM-53 |
| IT05.6       | POST xác nhận chuyển khoản       | Tạo trạng thái Pending                             | Trả về Login                                     | FAIL   | SCRUM-50 |
| IT06.1       | GET Admin Dashboard              | Hiển thị đầy đủ dashboard                          | Thiếu lịch sử thắng cuộc                         | FAIL   | SCRUM-51 |
| IT06.2       | GET Admin duyệt đơn thanh toán   | Chuyển Paid                                        | Thành công                                       | PASS   | -        |
| IT06.3       | GET Admin từ chối đơn thanh toán | Chuyển Cancel                                      | Thành công                                       | PASS   | -        |
| IT07.1       | GET Logout                       | Hủy session                                        | Thành công                                       | PASS   | -        |
| IT07.2       | GET my_bids sau logout           | Báo chưa đăng nhập                                 | Thành công                                       | PASS   | -        |
| IT08.1       | POST Admin thêm sản phẩm         | Thêm thành công                                    | Thành công                                       | PASS   | -        |
| IT08.2       | GET Admin xóa sản phẩm           | Xóa thành công                                     | Thành công                                       | PASS   | -        |

### Tổng hợp

* Tổng số test case: 13
* PASS: 8
* FAIL: 5
* Tỷ lệ PASS: 61.5%
* Tỷ lệ FAIL: 38.5%

---

## 5. Các lỗi phát hiện

### SCRUM-49 - IT05.3 Đặt giá hợp lệ bị Fatal Error

**Mô tả:**

Khi user gửi yêu cầu đặt giá hợp lệ, hệ thống phát sinh lỗi:

```text
Fatal error: validateBid(): Argument #1 ($product) must be of type array, null given
```

**Kết quả:**

* Không lưu được bid
* Không cập nhật giá sản phẩm
* Luồng đặt giá bị gián đoạn

**Đánh giá:** FAIL

---

### SCRUM-50 - IT05.6 Xác nhận chuyển khoản trả về Login

**Mô tả:**

Sau khi gửi yêu cầu xác nhận chuyển khoản, hệ thống chuyển hướng về trang đăng nhập thay vì tạo yêu cầu thanh toán.

**Kết quả:**

* Không tạo trạng thái Pending
* Không hoàn tất quy trình thanh toán

**Đánh giá:** FAIL

---

### SCRUM-51 - IT06.1 Admin Dashboard thiếu lịch sử thắng cuộc

**Mô tả:**

Dashboard admin tải thành công nhưng Postman không tìm thấy nội dung "LỊCH SỬ THẮNG CUỘC" theo yêu cầu kiểm thử.

**Kết quả:**

* Thiếu dữ liệu trên Dashboard
* Không đáp ứng đầy đủ yêu cầu quản trị

**Đánh giá:** FAIL

---

### SCRUM-52 - IT05.4 Đặt giá quá thấp không hiển thị thông báo lỗi đúng

**Mô tả:**

Khi user đặt giá thấp hơn giá hiện tại, hệ thống từ chối thao tác nhưng không hiển thị đúng thông báo lỗi theo yêu cầu test case.

**Kết quả:**

* Chức năng validation hoạt động
* Thông báo phản hồi chưa đúng mong đợi

**Đánh giá:** FAIL

---

### SCRUM-53 - IT05.5 My Bids không hiển thị dữ liệu sau khi đặt giá

**Mô tả:**

Sau khi hoàn thành bước đặt giá, trang my_bids không hiển thị dữ liệu đấu giá theo kết quả mong đợi của test case.

**Kết quả:**

* Không xác nhận được dữ liệu bid sau thao tác
* Ảnh hưởng việc theo dõi lịch sử đấu giá

**Đánh giá:** FAIL

---

## 6. Kết luận

Quá trình Integration Test đã kiểm tra các luồng nghiệp vụ chính từ User đến Admin.

Kết quả cho thấy phần lớn các chức năng cơ bản hoạt động ổn định như đăng nhập, đăng xuất, duyệt thanh toán, từ chối thanh toán, thêm sản phẩm và xóa sản phẩm.

Tuy nhiên vẫn phát hiện 5 lỗi tích hợp quan trọng:

* SCRUM-49 – Đặt giá hợp lệ bị Fatal Error
* SCRUM-50 – Xác nhận chuyển khoản trả về Login
* SCRUM-51 – Thiếu lịch sử thắng cuộc trên Admin Dashboard
* SCRUM-52 – Đặt giá quá thấp không hiển thị thông báo lỗi đúng
* SCRUM-53 – My Bids không hiển thị dữ liệu sau khi đặt giá

Các lỗi đã được ghi nhận trên Jira để nhóm phát triển tiếp tục phân tích và khắc phục.

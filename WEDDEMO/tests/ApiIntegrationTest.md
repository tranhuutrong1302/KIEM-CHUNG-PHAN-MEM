# API Integration Test - Royal Bid

## Môi trường kiểm thử

* XAMPP (Apache + MySQL)
* Postman
* phpMyAdmin

## Kết quả kiểm thử

| ID     | Chức năng                        | Expected Result                | Actual Result              | Status |
| ------ | -------------------------------- | ------------------------------ | -------------------------- | ------ |
| IT05.1 | GET my_bids (có session)         | Trả danh sách đấu giá của user | Trả dữ liệu JSON hợp lệ    | PASS   |
| IT05.2 | GET Trang chủ lấy product_id     | Lấy được product_id còn hạn    | Trả dữ liệu thành công     | PASS   |
| IT05.3 | POST Đặt giá hợp lệ              | Tạo bid thành công             | API trả success            | PASS   |
| IT05.4 | POST Đặt giá quá thấp            | Từ chối bid                    | API trả lỗi đúng           | PASS   |
| IT05.5 | GET my_bids sau khi đặt giá      | Hiển thị bid vừa tạo           | Dữ liệu cập nhật đúng      | PASS   |
| IT05.6 | POST Xác nhận chuyển khoản       | Tạo yêu cầu pending            | API trả kết quả thành công | PASS   |
| IT06.1 | GET Admin dashboard đầy đủ       | Hiển thị dữ liệu quản trị      | Thành công                 | PASS   |
| IT06.2 | GET Admin duyệt đơn thanh toán   | Chuyển trạng thái Paid         | Thành công                 | PASS   |
| IT06.3 | GET Admin từ chối đơn thanh toán | Chuyển trạng thái Cancel       | Thành công                 | PASS   |
| IT07.1 | GET Logout                       | Hủy session                    | Thành công                 | PASS   |
| IT07.2 | GET my_bids sau logout           | Trả lỗi chưa đăng nhập         | 401 Unauthorized           | PASS   |
| IT08.1 | POST Admin thêm sản phẩm mới     | Tạo sản phẩm mới               | Thành công                 | PASS   |
| IT08.2 | GET Admin xóa sản phẩm           | Xóa sản phẩm                   | Thành công                 | PASS   |

## Kết luận

Các API được kiểm thử theo luồng tích hợp giữa Client/Postman → REST API PHP → Session → Database MySQL.

Kết quả cho thấy các chức năng chính của hệ thống đấu giá hoạt động đúng với yêu cầu nghiệp vụ, dữ liệu được đồng bộ chính xác giữa các thành phần của hệ thống.

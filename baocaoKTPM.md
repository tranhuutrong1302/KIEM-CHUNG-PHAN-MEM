# BÁO CÁO BÀI TẬP LỚN MÔN KIỂM CHỨNG PHẦN MỀM
## ĐỀ TÀI: KIỂM CHỨNG VÀ HOÀN THIỆN HỆ THỐNG ĐẤU GIÁ TRỰC TUYẾN ROYAL BID



---

## PHẦN I: GIỚI THIỆU CHUNG VỀ HỆ THỐNG VÀ PHẠM VI KIỂM THỬ

### 1. Giới thiệu hệ thống Royal Bid
Royal Bid là hệ thống đấu giá trực tuyến cho phép người dùng đăng ký, đăng nhập, tham gia đấu giá các tài sản giá trị cao (như siêu xe, bất động sản, trang sức, đồng hồ hiệu) và tiến hành gửi xác nhận thanh toán sau khi thắng cuộc. Admin có quyền quản lý thành viên, phê duyệt/từ chối các giao dịch và đăng tải/xóa sản phẩm đấu giá.

* **Công nghệ phát triển:** PHP thuần (Backend) + MySQL (Database) + HTML/CSS/JS (Frontend - Bootstrap 5).
* **Môi trường vận hành thử nghiệm:** Localhost sử dụng phần mềm giả lập XAMPP (Apache, MySQL).

### 2. Mục tiêu kiểm chứng
* Xác minh tính đúng đắn của các quy tắc nghiệp vụ đấu giá (phân loại tự động sản phẩm, chuẩn hóa đường dẫn hình ảnh, luật tăng bước giá và kết thúc phiên).
* Đảm bảo tính liên kết hoạt động tốt giữa API Backend, Session đăng nhập của người dùng và tầng lưu trữ dữ liệu MySQL Database.
* Phát hiện, phân loại và khắc phục các lỗi tích hợp (Integration Bugs) để đảm bảo hệ thống vận hành ổn định, không xảy ra lỗi nghiêm trọng (Fatal Error).

### 3. Phạm vi kiểm thử
* **Tầng Đơn vị (Unit Test):** Kiểm thử các hàm xử lý logic độc lập tại `ApiHelper.php`, `ConfigDbTest.php`, và quy tắc nghiệp vụ tại `AuctionRules.php`.
* **Tầng Tích hợp (Integration Test):** Kiểm thử luồng tương tác giữa client và API hệ thống qua các kịch bản đăng ký, đăng nhập, đặt giá (Bid) và thanh toán (Payment).
* **Tầng Giao diện (Frontend Test):** Kiểm tra định dạng hiển thị tiền tệ, lọc sản phẩm theo category và hiển thị badge trạng thái trên trình duyệt.

---

## PHẦN II: THIẾT KẾ KỊCH BẢN KIỂM THỬ (TEST CASES)

### 1. Kịch bản Unit Test (Kiểm thử đơn vị)
Dựa trên cấu trúc mã nguồn tại thư mục `tests/Unit/` và `tests/CategoryUnitTest.php`, các kịch bản sau đã được xây dựng:

* **TC-BE-01 / UT-01:** Kiểm tra hàm `getCategory` trả về danh mục chính xác từ cơ sở dữ liệu nếu dòng dữ liệu đã được gán nhãn hợp lệ (Ví dụ: `Trang sức`).
* **TC-BE-02 / UT-02:** Kiểm tra hàm tự động nhận diện danh mục `Đồng hồ` dựa trên việc phân tích chuỗi ký tự trong tên sản phẩm (Ví dụ: `Đồng hồ Rolex`).
* **TC-BE-03 / UT-03:** Kiểm tra tự động nhận diện danh mục `Bất động sản` từ tên sản phẩm (Ví dụ: `Biệt thự biển`).
* **TC-BE-04 / UT-04:** Xác minh cấu trúc hàm `buildApiResponse` trả về đầy đủ các trường `success`, `message`, `data`, `code`.
* **TC-BE-05 / UT-05:** Kiểm tra hàm `normalizeImageUrl` chuyển đổi đường dẫn chứa ký tự đặc biệt hoặc khoảng trắng sang URL chuẩn hóa.
* **TC-BE-06 / UT-06:** Kiểm tra hàm `normalizeImageUrl` giữ nguyên định dạng nếu đầu vào đã là một URL tuyệt đối (http/https).
* **TC-BE-10 / UT-12:** Xác minh hàm `validateBid` từ chối các lượt đặt giá khi thời gian kết thúc đấu giá của sản phẩm đã trôi qua.
* **TC-BE-11 / UT-13:** Xác minh hàm `validateBid` từ chối lượt đặt giá có số tiền nhỏ hơn giá hiện tại cộng bước giá tối thiểu.

### 2. Kịch bản Integration Test (Kiểm thử tích hợp API)
Thực hiện giả lập client gửi các HTTP Request liên tiếp lên server thông qua Postman:

* **IT05.1:** Gửi yêu cầu GET tới `my_bids.php` khi session đăng nhập tồn tại -> Mong đợi trả về JSON chứa lịch sử đấu giá của User.
* **IT05.2:** Gửi yêu cầu GET tới trang chủ để lấy `product_id` còn hạn -> Mong đợi hiển thị danh sách sản phẩm hợp lệ và chọn được sản phẩm còn thời gian đấu giá.
* **IT05.3:** Gửi yêu cầu POST đặt giá hợp lệ tới API đặt giá -> Mong đợi lưu thành công lượt bid vào CSDL và cập nhật giá cao nhất của sản phẩm.
* **IT05.4:** Gửi yêu cầu POST đặt giá thấp hơn mức tối thiểu -> Mong đợi hệ thống từ chối và trả về mã lỗi 400 kèm thông báo tương thích.
* **IT05.6:** Gửi yêu cầu POST gửi xác nhận chuyển khoản cho sản phẩm thắng cuộc -> Mong đợi tạo mới một bản ghi giao dịch ở trạng thái `Pending` trong bảng `orders`.
* **IT06.1:** Admin truy cập màn hình Dashboard quản trị -> Mong đợi hiển thị bảng danh sách sản phẩm, thành viên và khu vực "Lịch sử thắng cuộc".
* **IT06.2 / IT06.3:** Admin gửi yêu cầu phê duyệt hoặc hủy bỏ đơn thanh toán của người dùng -> Mong đợi trạng thái đơn chuyển dịch thành `Paid` hoặc `Cancel` tương ứng trong CSDL.
* **IT07.2:** Gửi yêu cầu GET tới `my_bids.php` sau khi logout -> Mong đợi hệ thống yêu cầu đăng nhập lại.
* **IT08.2:** Admin gửi yêu cầu GET xóa sản phẩm -> Mong đợi thao tác thành công và dữ liệu được cập nhật đúng trong CSDL.

---

## PHẦN III: KẾT QUẢ THỰC THI KIỂM THỬ

### 1. Bảng tổng hợp trạng thái thực thi Test Cases (Sau khi khắc phục)

| Mã TC | Tên kịch bản test | Kết quả mong đợi | Kết quả thực tế | Trạng thái |
| :--- | :--- | :--- | :--- | :---: |
| **UT-01** | Lấy danh mục gán sẵn | Trả về danh mục khớp dữ liệu | Trả về đúng danh mục | **PASS** |
| **UT-02** | Nhận diện Đồng hồ từ tên | Tự động phân loại "Đồng hồ" | Tự động phân loại đúng | **PASS** |
| **UT-03** | Nhận diện Bất động sản | Tự động phân loại "Bất động sản" | Tự động phân loại đúng | **PASS** |
| **UT-04** | Cấu trúc API Response | Định dạng JSON đủ key chuẩn | Trả về đúng cấu trúc | **PASS** |
| **UT-05** | Chuẩn hóa ảnh tương đối | Mã hóa thực thể URL an toàn | Đầu ra khớp chuẩn URL | **PASS** |
| **UT-06** | Giữ nguyên URL tuyệt đối | Không thay đổi link http/https | Giữ nguyên cấu trúc link | **PASS** |
| **IT05.1**| GET my_bids khi có session | Trả về JSON danh sách bid | Trả JSON success=true | **PASS** |
| **IT05.2**| GET trang chủ lấy product_id | Hiển thị sản phẩm còn hạn | Hiển thị đúng danh sách sản phẩm | **PASS** |
| **IT05.3**| POST đặt giá hợp lệ | Lưu bid và cập nhật giá | Chạy ổn định, không crash | **PASS** |
| **IT05.4**| POST đặt giá quá thấp | Từ chối và hiển thị thông báo | Hiển thị giá tối thiểu chi tiết | **PASS** |
| **IT05.5**| GET my_bids sau khi đặt | Hiển thị lịch sử đấu giá | Hiển thị đúng danh sách bid | **PASS** |
| **IT05.6**| POST xác nhận chuyển khoản | Tạo bản ghi order Pending | Chuyển hướng đúng trang Frontend | **PASS** |
| **IT06.1**| GET Admin Dashboard | Tải đủ dữ liệu và lịch sử | Hiển thị đủ bảng lịch sử thắng | **PASS** |
| **IT06.2**| Admin duyệt thanh toán | Cập nhật đơn hàng thành Paid | Cập nhật thành công | **PASS** |
| **IT06.3**| Admin hủy đơn thanh toán | Cập nhật đơn hàng thành Cancel | Hủy thành công | **PASS** |
| **IT07.1**| GET API Đăng xuất | Hủy session trên máy chủ | Đăng xuất thành công | **PASS** |
| **IT07.2**| GET my_bids sau logout | Yêu cầu đăng nhập lại | Trả về đúng trạng thái | **PASS** |
| **IT08.1**| Admin thêm sản phẩm | Tạo mới bản ghi sản phẩm | Thêm sản phẩm thành công | **PASS** |
| **IT08.2**| Admin xóa sản phẩm | Xóa bản ghi sản phẩm | Xóa sản phẩm thành công | **PASS** |

### 2. Thống kê số liệu kiểm thử tích hợp (API Integration Test)
* **Tổng số kịch bản thực hiện:** 13
* **Số kịch bản PASS:** 13 (Tỷ lệ: 100%)
* **Số kịch bản FAIL:** 0 (Tỷ lệ: 0%)

*Nhận xét:* Sau quá trình kiểm chứng và khắc phục, hệ thống đã vượt qua tất cả các kịch bản kiểm thử. Các lỗi nghiêm trọng về kiểu dữ liệu (Fatal Error) và lỗi điều hướng (Redirect) đã được giải quyết hoàn toàn, đảm bảo luồng vận hành từ người dùng đến quản trị viên diễn ra thông suốt.

### 3. Minh chứng Postman cần chèn vào báo cáo
Để hoàn thiện bản nộp, nên chèn ảnh chụp màn hình Postman hiển thị trạng thái `PASS` cho các request sau:

* `00.1 - Ket noi MySQL (test_db.php)`
* `00.2 - Trang chu load san pham`
* `05.1 - GET my_bids (co session)`
* `05.2 - GET Trang chu - lay product_id con han`
* `05.3 - POST Dat gia hop le`
* `05.4 - POST Dat gia qua thap`
* `05.5 - GET my_bids sau khi dat gia`
* `05.6 - POST Xac nhan chuyen khoan (confirm_pay)`
* `06.1 - GET Admin dashboard day du`
* `06.2 - GET Admin duyet don thanh toan`
* `06.3 - GET Admin tu choi don thanh toan`
* `07.1 - GET Logout`
* `07.2 - GET my_bids sau logout -> phai ve login`
* `08.1 - POST Admin them san pham moi`
* `08.2 - GET Admin xoa san pham`

File collection và environment để đối chiếu:
* [Royal_Bid.postman_collection.json](WEDFULLSOURCODE/tests/Integration/Royal_Bid.postman_collection.json)
* [Royal_Bid.postman_environment.json](WEDFULLSOURCODE/tests/Integration/Royal_Bid.postman_environment.json)

---

## PHẦN IV: CHI TIẾT CÁC LỖI PHÁT HIỆN VÀ KẾT QUẢ KHẮC PHỤC

### 1. SCRUM-49: Fatal Error tại hàm `validateBid()`
* **Mô tả:** Khi truyền giá trị `null` vào tham số `$product`, hệ thống ném ra lỗi `Fatal error: Argument #1 ($product) must be of type array`.
* **Nguyên nhân:** Thiếu kiểm tra sự tồn tại của sản phẩm trước khi gọi hàm validate.
* **Kết quả khắc phục:** 
    - Cập nhật kiểu dữ liệu tham số thành nullable `?array`.
    - Bổ sung điều kiện kiểm tra `if ($product === null)` để trả về mã lỗi `PRODUCT_NOT_FOUND` thay vì crash hệ thống.

### 2. SCRUM-50: Lỗi điều hướng API thanh toán về trang Login
* **Mô tả:** Request xác nhận thanh toán bị redirect về `login.php` gây mất session dù user đã đăng nhập.
* **Nguyên nhân:** Cấu hình đường dẫn điều hướng (Header Location) trong `db.php` và các file auth trỏ sai về folder `/backend/` thay vì `/frontend/`.
* **Kết quả khắc phục:** Đồng bộ toàn bộ các lệnh `header("Location: ...")` về các file `.html` tương ứng tại thư mục frontend (Ví dụ: `login.php` $\rightarrow$ `../frontend/login.html`).

### 3. SCRUM-51: Thiếu vùng hiển thị lịch sử thắng cuộc tại Admin Dashboard
* **Mô tả:** Giao diện quản trị không hiển thị danh sách tài sản đã đấu giá thành công.
* **Nguyên nhân:** Chưa tối ưu truy vấn kết hợp (JOIN) và thiếu cơ chế phân quyền truy cập chặt chẽ cho trang quản trị.
* **Kết quả khắc phục:** 
    - Xây dựng truy vấn SQL JOIN giữa bảng `orders`, `users` và `products` để lấy thông tin người thắng cuộc.
    - Triển khai hàm `checkAdminLogin()` để ngăn chặn truy cập trái phép vào Dashboard.

### 4. SCRUM-52: Thông báo lỗi đặt giá thấp không rõ ràng
* **Mô tả:** Hệ thống từ chối lượt bid quá thấp nhưng thông báo chung chung, không cho người dùng biết mức giá tối thiểu cần đặt.
* **Nguyên nhân:** Message trả về từ hàm nghiệp vụ không bao gồm giá trị tính toán thực tế.
* **Kết quả khắc phục:** Cập nhật message lỗi bằng cách nối chuỗi giá trị `$minRequired` đã được định dạng `number_format()`, giúp người dùng biết chính xác số tiền cần bid.

### 5. SCRUM-53: Trang "Lịch sử của tôi" (My Bids) hiển thị trống
* **Mô tả:** Người dùng không thấy lịch sử đấu giá dù dữ liệu đã được lưu trong CSDL.
* **Nguyên nhân:** Truy vấn SQL thiếu điều kiện lọc `user_id` hoặc giá trị session không được truyền chính xác vào câu lệnh query.
* **Kết quả khắc phục:** 
    - Chuẩn hóa lại câu lệnh SQL với điều kiện `WHERE b.user_id = $user_id`.
    - Bổ sung validate `(int)$_SESSION['user_id']` để đảm bảo ID người dùng luôn hợp lệ trước khi thực thi query.

---

## PHẦN V: KẾT LUẬN VÀ ĐÁNH GIÁ CUỐI CÙNG

### 1. Kết luận chung
Thông qua quá trình kiểm chứng phần mềm, hệ thống Royal Bid đã được rà soát kỹ lưỡng từ tầng đơn vị đến tầng tích hợp. Tất cả 5 lỗi nghiêm trọng phát hiện trong giai đoạn đầu (SCRUM-49 $\rightarrow$ SCRUM-53) đã được khắc phục triệt để. Hệ thống hiện tại vận hành ổn định, đáp ứng đúng các yêu cầu về nghiệp vụ đấu giá và bảo mật cơ bản.

### 2. Bài học rút ra
* Việc áp dụng Unit Test giúp phát hiện sớm các lỗi ép kiểu dữ liệu (Type Error) trước khi triển khai lên giao diện.
* Khi làm việc với kiến trúc tách biệt Backend (API) và Frontend (HTML), việc đồng bộ đường dẫn điều hướng (Redirect Paths) là cực kỳ quan trọng để tránh mất session.
* Việc ghi log chi tiết (Error Logging) giúp rút ngắn thời gian tìm kiếm nguyên nhân gây ra lỗi "trống dữ liệu" trong các truy vấn SQL phức tạp.

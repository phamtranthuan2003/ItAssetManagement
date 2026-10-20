# HỆ THỐNG QUẢN LÝ THIẾT BỊ KHO (PC / LAPTOP) - PHP PURE MVC

Hệ thống web quản lý thiết bị công nghệ thông tin trong kho doanh nghiệp (PC để bàn, Laptop), cấp phát, chuyển giao, thu hồi, bảo dưỡng và theo dõi vòng đời thiết bị. Xây dựng hoàn toàn bằng **PHP thuần theo kiến trúc MVC**, chuẩn kết nối **PDO Prepared Statements**, giao diện **Bootstrap 5**, tối ưu chạy trên môi trường **XAMPP (Apache + MySQL/MariaDB)**.

---

## 1. CÔNG NGHỆ SỬ DỤNG
- **Ngôn ngữ lập trình:** PHP 8.0+ (PHP thuần, hướng đối tượng OOP, không dùng framework nặng).
- **Cơ sở dữ liệu:** MySQL / MariaDB (Kết nối qua PDO Singleton, 100% Prepared Statements chống SQL Injection).
- **Giao diện (Frontend):** Bootstrap 5.3, Bootstrap Icons, responsive cho mọi màn hình máy tính và thiết bị di động.
- **Bảo mật:**
  - Băm mật khẩu một chiều với thuật toán Bcrypt bằng `password_hash()` và `password_verify()`.
  - Chống tấn công CSRF thông qua token ngẫu nhiên `csrf_token()` trên mọi form POST.
  - Chống tấn công XSS thông qua hàm xử lý `e()` (`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`).
  - Phân quyền theo vai trò (RBAC): **Quản trị viên (Admin)** và **Nhân viên kho (Warehouse)**.
- **Định tuyến (Routing):** Bộ định tuyến URL thân thiện (Friendly URLs) qua file `.htaccess` và Front Controller `public/index.php`.

---

## 2. CẤU TRÚC THƯ MỤC DỰ ÁN (MVC ARCHITECTURE)

```text
warehouse_management/
├── app/
│   ├── controllers/            # Tầng điều hướng & tiếp nhận logic nghiệp vụ
│   │   ├── AuthController.php          # Đăng nhập, đăng xuất, đổi mật khẩu
│   │   ├── DashboardController.php     # Bảng điều khiển thống kê tổng quan
│   │   ├── DeviceController.php        # CRUD thiết bị, tìm kiếm, lọc, xuất Excel
│   │   ├── DepartmentController.php    # Quản lý danh mục phòng ban
│   │   ├── EmployeeController.php      # Quản lý nhân viên & thu hồi hàng loạt khi nghỉ việc
│   │   ├── TicketController.php        # Lập phiếu cấp phát, chuyển giao, thu hồi, in biên bản
│   │   ├── MaintenanceController.php   # Quản lý sửa chữa, bảo dưỡng & nâng cấp phần cứng
│   │   ├── UserController.php          # Quản lý tài khoản và phân quyền (Admin)
│   │   └── ReportController.php        # Báo cáo thống kê chuyên sâu
│   ├── models/                 # Tầng tương tác Cơ sở dữ liệu qua PDO
│   │   ├── User.php                    # Bảng users
│   │   ├── Device.php                  # Bảng devices
│   │   ├── DeviceSpec.php              # Bảng device_specs (Cấu hình PC/Laptop)
│   │   ├── Department.php              # Bảng departments
│   │   ├── Employee.php                # Bảng employees
│   │   ├── Ticket.php                  # Bảng tickets
│   │   ├── TicketDetail.php            # Bảng ticket_details
│   │   ├── DeviceHistory.php           # Bảng device_history (Nhật ký vòng đời)
│   │   └── Maintenance.php             # Bảng maintenances (Sửa chữa, nâng cấp)
│   └── views/                  # Tầng giao diện người dùng (Bootstrap 5)
│       ├── layouts/                    # Layout chung (Sidebar, Header, Alert flash)
│       ├── auth/                       # Đăng nhập, Hồ sơ cá nhân
│       ├── dashboard/                  # Dashboard chỉ số & cảnh báo
│       ├── devices/                    # Danh sách, thêm, sửa, chi tiết thiết bị
│       ├── departments/                # Quản lý phòng ban
│       ├── employees/                  # Quản lý nhân sự & hồ sơ thiết bị đang giữ
│       ├── tickets/                    # Phiếu cấp phát, chuyển giao, thu hồi & In biên bản
│       ├── maintenance/                # Danh sách & lập phiếu bảo trì/nâng cấp
│       ├── users/                      # Quản trị tài khoản
│       └── reports/                    # Báo cáo tổng hợp
├── config/
│   ├── config.php              # Cấu hình DB, App name, tự động nhận diện Base URL
│   └── database.php            # Lớp kết nối PDO Singleton
├── core/
│   ├── Auth.php                # Quản lý phiên đăng nhập và phân quyền RBAC
│   ├── Controller.php          # Lớp Controller cơ sở (render view, json, validate)
│   ├── Helper.php              # Tiện ích bảo mật (XSS, CSRF, URL, định dạng ngày/tiền tệ)
│   ├── Model.php               # Lớp Model cơ sở (truy vấn PDO, transaction)
│   └── Router.php              # Bộ định tuyến URL đẹp
├── public/                     # Thư mục gốc công khai của Web Server
│   ├── assets/
│   │   ├── css/style.css       # Tùy biến giao diện và định dạng in ấn
│   │   └── js/main.js          # Xử lý tương tác động (chọn PC/Laptop, chọn thiết bị)
│   ├── .htaccess               # Cấu hình Rewrite URL của Apache
│   └── index.php               # Front Controller tiếp nhận mọi request
├── .htaccess                   # Root .htaccess tự động chuyển tiếp request vào /public
├── database.sql                # File tạo CSDL đầy đủ khóa ngoại và dữ liệu mẫu
└── README.md                   # Hướng dẫn chi tiết
```

---

## 3. THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

Cơ sở dữ liệu tên là `warehouse_db` sử dụng bảng mã `utf8mb4_unicode_ci` hỗ trợ tiếng Việt có dấu:

1. **`users`**: Quản lý tài khoản quản trị và thủ kho (`id`, `username`, `password`, `fullname`, `email`, `role`, `status`).
2. **`departments`**: Quản lý phòng ban sử dụng thiết bị (`id`, `code`, `name`, `description`).
3. **`employees`**: Quản lý nhân viên công ty (`id`, `code`, `fullname`, `department_id`, `position`, `status`).
4. **`devices`**: Quản lý thông tin chung thiết bị (`id`, `device_code`, `type`, `brand`, `model`, `serial_number`, `import_date`, `warranty_expiry`, `condition_state`, `status`, `current_employee_id`, `current_department_id`, `is_temporary`, `expected_return_date`, `notes`).
5. **`device_specs`**: Quản lý cấu hình chi tiết theo quan hệ 1-1 với `devices` (`id`, `device_id`, `cpu`, `mainboard`, `ram_gb`, `ram_type`, `ssd_capacity`, `hdd_capacity`, `gpu`, `screen_size`, `notes`).
6. **`tickets`**: Phiếu giao dịch cấp phát / chuyển giao / thu hồi (`id`, `ticket_code`, `ticket_type`, `ticket_date`, `created_by`, `from_employee_id`, `to_employee_id`, `is_temporary`, `status`, `reason`, `notes`).
7. **`ticket_details`**: Chi tiết các thiết bị trong một phiếu (`id`, `ticket_id`, `device_id`, `accessories`, `returned_condition`, `notes`).
8. **`device_history`**: Nhật ký toàn diện mọi thao tác và vòng đời thiết bị (`id`, `device_id`, `ticket_id`, `action_type`, `action_date`, `performed_by`, `description`).
9. **`maintenances`**: Nhật ký bảo dưỡng, sửa chữa và nâng cấp linh kiện (`id`, `device_id`, `maintenance_type`, `start_date`, `end_date`, `cost`, `provider`, `description`, `status`).

---

## 4. HƯỚNG DẪN CÀI ĐẶT VÀ CHẠY TRÊN XAMPP

### Bước 1: Khởi động XAMPP
- Mở **XAMPP Control Panel**.
- Bấm nút **Start** ở cả 2 dịch vụ: **Apache** và **MySQL**.

### Bước 2: Đặt mã nguồn vào thư mục XAMPP
Bạn có 2 cách chạy:

- **Cách 1 (Khuyên dùng):** Sao chép toàn bộ thư mục dự án vào `htdocs`:
  - Đường dẫn đích: `C:\xampp\htdocs\warehouse_management` (hoặc tên tùy chọn).
- **Cách 2:** Tạo VirtualHost trỏ trực tiếp vào thư mục `D:\project\public`, hoặc chạy qua lệnh PHP CLI:
  ```powershell
  cd D:\project
  C:\xampp\php\php.exe -S localhost:8000 -t public
  ```

### Bước 3: Tạo và Import Cơ sở dữ liệu
- Mở trình duyệt web truy cập: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Nhấp vào tab **Databases (Cơ sở dữ liệu)**, tạo mới DB tên là: `warehouse_db` (chọn Collation `utf8mb4_unicode_ci`).
- Chọn cơ sở dữ liệu `warehouse_db`, bấm vào tab **Import (Nhập)**.
- Chọn file `database.sql` có sẵn trong thư mục dự án và bấm nút **Import** ở cuối trang.
*(Hoặc dùng lệnh MySQL CLI: `C:\xampp\mysql\bin\mysql.exe -u root warehouse_db < database.sql`)*

### Bước 4: Kiểm tra cấu hình kết nối CSDL (nếu cần)
Mở file `config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'warehouse_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Mặc định trên XAMPP mật khẩu root là rỗng
```
*Hệ thống đã tích hợp cơ chế tự động nhận diện `BASE_PATH` và `BASE_URL`, dù bạn đặt thư mục con tên gì trong `htdocs` thì URL vẫn tự động khớp chuẩn xác!*

### Bước 5: Bật mod_rewrite trên Apache (để dùng URL đẹp)
Mặc định XAMPP đã bật sẵn `mod_rewrite`. Nếu chưa bật:
1. Mở file `C:\xampp\apache\conf\httpd.conf`.
2. Bỏ dấu `#` trước dòng: `LoadModule rewrite_module modules/mod_rewrite.so`.
3. Khởi động lại Apache trong XAMPP Control Panel.

### Bước 6: Truy cập ứng dụng
Mở trình duyệt web và truy cập:
- Nếu đặt trong `htdocs/warehouse_management`:
  👉 [http://localhost/warehouse_management/](http://localhost/warehouse_management/)
  *(File `.htaccess` ở thư mục gốc sẽ tự động chuyển tiếp vào giao diện chính)*
- Nếu chạy qua máy chủ built-in:
  👉 [http://localhost:8000/](http://localhost:8000/)

---

## 5. TÀI KHOẢN ĐĂNG NHẬP MẪU

Hệ thống được tạo sẵn 2 tài khoản để bạn đăng nhập thử nghiệm ngay:

| Vai trò | Tên đăng nhập | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin` | `password123` | Toàn quyền quản trị hệ thống, quản lý tài khoản người dùng, cấu hình kho, xóa dữ liệu |
| **Nhân viên kho (Warehouse)** | `thukho` | `password123` | Quản lý thiết bị, lập phiếu cấp phát/thu hồi, bảo dưỡng, xuất báo cáo |

---

## 6. HƯỚNG DẪN TRẢI NGHIỆM CÁC TÍNH NĂNG ĐẶC BIỆT

1. **Quản lý thiết bị linh hoạt theo cấu hình:**
   - Khi chọn loại thiết bị là **PC**: Form tự động mở thêm trường **Mainboard (Bo mạch chủ)**.
   - Khi chọn loại thiết bị là **Laptop**: Form tự động mở thêm trường **Màn hình**.
   - Bộ lọc nâng cao hỗ trợ lọc theo: Loại máy, Hãng sản xuất, Trạng thái kho, Tình trạng máy, CPU, RAM tối thiểu, SSD, và Phòng ban đang dùng.
2. **Cấp phát mượn tạm thời & Cảnh báo quá hạn:**
   - Khi lập phiếu cấp phát, nếu tích chọn "Cấp phát tạm thời" và chọn hạn trả, hệ thống sẽ tự động giám sát.
   - Nếu quá ngày trả mà chưa thu hồi, trang Dashboard và danh sách thiết bị sẽ bật **Cảnh báo đỏ** kèm danh sách trực quan.
3. **Cảnh báo sắp hết hạn bảo hành:**
   - Hệ thống tự động tính khoảng cách ngày bảo hành của thiết bị, cảnh báo các máy còn dưới 30 ngày bảo hành.
4. **Quy trình Thu hồi tự động & Thông minh:**
   - Thu hồi thiết bị cho phép ghi nhận tình trạng khi trả:
     - Nếu trả **Tốt** -> Thiết bị tự động chuyển về trạng thái **Trong kho (in_stock)**.
     - Nếu trả **Hỏng** -> Thiết bị tự động chuyển sang trạng thái **Bảo trì (maintenance)**.
     - Xóa người sử dụng và phòng ban hiện tại.
5. **Thu hồi hàng loạt khi nhân sự nghỉ việc:**
   - Vào menu **Nhân viên** -> Chọn nhân viên -> Bấm vào hồ sơ để xem các thiết bị đang giữ.
   - Bấm nút **"Thu hồi tất cả thiết bị"**: Toàn bộ máy nhân viên đang giữ sẽ được đưa vào 1 phiếu thu hồi duy nhất trong 1 click và có tùy chọn chuyển trạng thái nhân sự thành **Đã nghỉ việc**.
6. **In Biên bản bàn giao chuẩn hành chính:**
   - Ở bất kỳ phiếu cấp phát/chuyển giao/thu hồi nào, bấm nút **In biên bản**.
   - Giao diện in được thiết kế chuẩn khổ A4, trang trọng với Quốc hiệu tiêu ngữ, bảng thông số kỹ thuật, phụ kiện đi kèm và 4 ô chữ ký: Bên giao, Bên nhận, Thủ kho, Lãnh đạo phê duyệt. Bấm nút **In biên bản (Ctrl + P)** để in ra giấy hoặc lưu thành file PDF.
7. **Xuất danh sách thiết bị ra Excel / CSV:**
   - Tại trang danh sách thiết bị hoặc báo cáo, bấm nút **Xuất Excel / CSV**. File xuất ra đã được chèn UTF-8 BOM, đảm bảo mở trực tiếp bằng Microsoft Excel mà không bao giờ bị lỗi font tiếng Việt.
#   I t A s s e t M a n a g e m e n t  
 
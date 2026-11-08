# Thế Giới Điện Thoại (PHP MVC)

Chạy trên máy bằng **Docker Compose**, đưa lên mạng bằng **InfinityFree**. Không cần Laragon/XAMPP nữa.

Web chạy ở **gốc tên miền** (`http://localhost:8080/` và `https://tenban.infinityfreeapp.com/`), không còn tiền tố `/project1`.

---

## 1. Chạy trên máy bằng Docker

### Cài đặt một lần
1. Cài **Docker Desktop for Windows**: https://www.docker.com/products/docker-desktop/ (bật WSL 2 khi được hỏi), khởi động lại máy.
2. Mở Docker Desktop, đợi chữ *Engine running*.

### Khởi động
Mở terminal tại thư mục project:

```bash
docker compose up -d --build
```

| Dịch vụ    | Địa chỉ                          | Ghi chú                         |
|------------|----------------------------------|---------------------------------|
| Website    | http://localhost:8080            |                                 |
| phpMyAdmin | http://localhost:8081            | tự đăng nhập root               |
| MariaDB    | `localhost:3307` (user `root` / pass `root`) | dùng cho HeidiSQL, DBeaver... |

Lần đầu chạy, Docker tự tạo DB `my_store` từ `database/init/01_schema.sql` (bảng + 4 danh mục).

### Thêm sản phẩm mẫu

```bash
docker compose exec web php seed_products.php
```

```bash
docker compose exec web php seed_product_professional.php
```

### Tạo tài khoản admin
1. Vào http://localhost:8080/Account/register đăng ký một tài khoản.
2. Mở phpMyAdmin → `my_store` → tab **SQL**, chạy (thay tên đăng nhập của bạn):
   ```sql
   UPDATE account SET role = 'admin' WHERE username = 'ten_dang_nhap';
   ```
3. Đăng xuất rồi đăng nhập lại.

### Lệnh hay dùng

```bash
docker compose down
```
(tắt; dữ liệu DB vẫn giữ trong volume `db_data`)

```bash
docker compose down -v
```
(tắt **và xóa sạch DB** – lần sau chạy sẽ tạo lại từ file SQL)

```bash
docker compose logs -f web
```

Code được mount trực tiếp vào container, sửa file xong chỉ cần F5.

---

## 2. Đưa lên mạng bằng InfinityFree

### Bước 1 – Tạo hosting
1. Đăng ký tại https://www.infinityfree.com → **Create Account** → chọn subdomain miễn phí (vd `tgdd-shop.infinityfreeapp.com`).
2. Vào **Control Panel** (VistaPanel) của tài khoản hosting.

### Bước 2 – Tạo database
1. Control Panel → **MySQL Databases** → tạo DB tên `my_store` → được tên đầy đủ dạng `if0_12345678_my_store`.
2. Ghi lại 4 thông tin hiện ở trang đó: **MySQL Hostname** (`sqlXXX.infinityfree.com`), **Database Name**, **Username** (`if0_12345678`), **Password** (mật khẩu hosting – xem ở trang *Account → Account Details*).

### Bước 3 – Đưa dữ liệu lên
Cách A – **chuyển nguyên dữ liệu đang có trên Docker** (khuyên dùng):
1. Mở http://localhost:8081 → chọn `my_store` → **Export** → *Quick*, định dạng SQL → **Export** → được file `my_store.sql`.
2. Trên InfinityFree: Control Panel → **phpMyAdmin** → chọn DB `if0_..._my_store` → **Import** → chọn file `my_store.sql` → **Import**.

Cách B – DB trống: import file `database/init/01_schema.sql` theo cách tương tự.

> Docker dùng **MariaDB** giống InfinityFree nên file export import được ngay, không bị lỗi collation `utf8mb4_0900_ai_ci`.

### Bước 4 – Cấu hình kết nối DB
Copy `app/config/db.local.example.php` thành `app/config/db.local.php` rồi điền 4 thông tin ở Bước 2:

```php
return [
    'host'     => 'sql123.infinityfree.com',
    'port'     => '3306',
    'name'     => 'if0_12345678_my_store',
    'user'     => 'if0_12345678',
    'password' => 'mat_khau_hosting',
];
```

File này đã được thêm vào `.gitignore` nên mật khẩu không bị đẩy lên GitHub. Trên Docker **không** cần file này (Docker truyền biến môi trường).

### Bước 5 – Upload code
Dùng **FileZilla** (thông tin FTP ở Control Panel → *FTP Details*: host `ftpupload.net`, user `if0_12345678`, port 21) hoặc **Online File Manager**.

Upload vào thư mục **`htdocs/`** (xóa file `index2.html` mặc định trong đó):

```
htdocs/
├── .htaccess        ← bắt buộc (bật đường dẫn đẹp + chặn truy cập app/)
├── index.php
├── app/             ← nhớ có app/config/db.local.php
└── public/
```

**Không cần** upload: `.git/`, `docker/`, `database/`, `Dockerfile`, `docker-compose.yml`, `seed_*.php`, `README.md`.

> FileZilla mặc định ẩn file bắt đầu bằng dấu chấm – kiểm tra kỹ `.htaccess` đã lên chưa (Server → Force showing hidden files).

### Bước 6 – Kiểm tra
- Mở `https://tenban.infinityfreeapp.com` (subdomain mới có thể mất tới vài giờ mới truy cập được).
- Vào Control Panel bật **SSL miễn phí** (*Free SSL Certificates*) để có HTTPS.
- Nếu dùng **Đăng nhập Google**: vào Google Cloud Console → Credentials → OAuth Client → thêm tên miền mới vào **Authorized JavaScript origins**.

### Lỗi thường gặp
| Hiện tượng | Cách xử lý |
|---|---|
| "Không kết nối được cơ sở dữ liệu" | Sai thông tin trong `db.local.php` – host phải là `sqlXXX.infinityfree.com`, **không** phải `localhost`. |
| Trang chủ được nhưng bấm link bị 404 | Thiếu file `.htaccess` trong `htdocs/`. |
| Ảnh sản phẩm không hiện | Chưa upload thư mục `public/images/`. |
| Không gửi được email xác nhận đơn | InfinityFree không hỗ trợ hàm `mail()` – web vẫn đặt hàng bình thường, chỉ không gửi mail. |
| Upload ảnh thất bại | File quá lớn (InfinityFree giới hạn ~10MB/file). |

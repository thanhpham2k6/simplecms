# SimpleCMS

![SimpleCMS Logo](https://img.shields.io/badge/SimpleCMS-v1.0.0-blue)![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-purple)
![License](https://img.shields.io/badge/license-MIT-green)

**SimpleCMS** là một hệ thống quản lý nội dung mã nguồn mở được xây dựng bằng PHP, MySQL, lấy cảm hứng từ WordPress với giao diện thân thiện và dễ sử dụng.

## ✨ Tính năng

- 📝 Quản lý bài viết với trình soạn thảo
- 📁 Hệ thống phân loại (Categories)
- 👥 Quản lý người dùng & phân quyền
- 🎨 Hệ thống theme có thể thay đổi
- 🔌 Hệ thống plugin mở rộng
- 📷 Upload và quản lý media
- 🔒 Bảo mật cao (XSS, CSRF, SQL Injection protection)
- 🔍 SEO-friendly URLs
- 📱 Responsive design

## 🚀 Cài đặt nhanh

### Yêu cầu hệ thống

- Ubuntu 20.04 LTS hoặc cao hơn
- PHP 7.4+
- MySQL 5.7+ hoặc MariaDB 10.3+
- Apache 2.4+ với mod_rewrite
- 512MB RAM tối thiểu
- 1GB dung lượng đĩa

### Cài đặt tự động

```bash
# Clone repository
git clone https://github.com/thanhpham2k6/simplecms.git
cd simplecms

# Chạy script cài đặt
sudo chmod +x scripts/install.sh
sudo ./scripts/install.sh
```

Script sẽ tự động:
- Cài đặt LAMP stack (nếu chưa có)
- Tạo database và user
- Cấu hình Apache
- Thiết lập quyền file
- Tạo file config

### Cài đặt thủ công

Xem hướng dẫn chi tiết tại [INSTALLATION.md](docs/INSTALLATION.md)

## 📖 Tài liệu

- [Hướng dẫn cài đặt](docs/INSTALLATION.md)
- [Cấu hình](docs/CONFIGURATION.md)
- [Hướng dẫn phát triển](docs/DEVELOPMENT.md)
- [API Documentation](docs/API.md)

## 🛠️ Cấu hình

Sau khi cài đặt, truy cập:

```
http://your-domain.com/install.php
```

Điền thông tin admin và hoàn tất cài đặt.

## 📝 Sử dụng

### Đăng nhập Admin

```
URL: http://your-domain.com/admin
```

### Tạo bài viết mới

1. Đăng nhập vào admin panel
2. Chọn "Thêm bài viết" từ menu
3. Điền thông tin và xuất bản

### Cài đặt Theme

1. Upload theme vào thư mục `/themes/`
2. Vào Admin → Giao diện
3. Kích hoạt theme mới

### Cài đặt Plugin

1. Upload plugin vào thư mục `/plugins/`
2. Vào Admin → Plugin
3. Kích hoạt plugin

## 🔧 Backup & Restore

### Backup tự động

```bash
# Cấu hình cron job
crontab -e

# Thêm dòng sau (backup mỗi ngày lúc 2h sáng)
0 2 * * * /var/www/html/simplecms/scripts/backup.sh
```

### Backup thủ công

```bash
cd /var/www/html/simplecms
sudo ./scripts/backup.sh
```

### Restore

```bash
# Restore database
mysql -u simplecms_user -p simplecms < backup_file.sql

# Restore files
tar -xzf backup_files.tar.gz -C /var/www/html/
```

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp! Vui lòng:

1. Fork repository
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 License

SimpleCMS được phát hành dưới [MIT License](LICENSE).

## 🐛 Báo lỗi

Nếu bạn phát hiện lỗi, vui lòng tạo issue tại:
https://github.com/thanhpham2k6/simplecms/issues

## 📧 Liên hệ

- Website: https://thanhtechno.id.vn
- Email: thanh.pvt06@gmail.com
- GitHub: https://github.com/thanhpham2k6/simplecms

## 🙏 Credits

- Developed by Thanhpham
- Inspired by WordPress
- Icons by [Lucide](https://lucide.dev)

## ⭐ Support

Nếu bạn thấy project hữu ích, hãy cho chúng tôi một star trên GitHub!

---

**Happy blogging with SimpleCMS!** 🚀
```


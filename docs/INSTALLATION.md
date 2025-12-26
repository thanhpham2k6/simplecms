# Hướng dẫn cài đặt SimpleCMS

## Phương pháp 1: Cài đặt tự động (Khuyến nghị)

### Ubuntu/Debian
```bash
# Clone repository
git clone https://github.com/yourusername/simplecms.git
cd simplecms

# Chạy script cài đặt
sudo chmod +x install.sh
sudo ./install.sh
```

Script sẽ hỏi các thông tin:
- Domain name (mặc định: localhost)
- Database name (mặc định: simplecms)
- Database user (mặc định: simplecms_user)
- Database password

### Hoàn tất cài đặt

1. Truy cập: `http://your-domain.com`
2. Tạo admin user đầu tiên thông qua MySQL:
```bash
sudo mysql
USE simplecms;

INSERT INTO users (username, email, password, role, created_at) 
VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());
-- Password: password
```

3. Đăng nhập: `http://your-domain.com/login.php`

## Phương pháp 2: Cài đặt thủ công

### Bước 1: Cài đặt LAMP Stack
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Cài Apache
sudo apt install apache2 -y

# Cài MySQL
sudo apt install mysql-server -y

# Cài PHP
sudo apt install php php-mysql php-gd php-mbstring php-xml php-curl libapache2-mod-php -y
```

### Bước 2: Tạo Database
```bash
sudo mysql

# Trong MySQL prompt
CREATE DATABASE simplecms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'simplecms_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON simplecms.* TO 'simplecms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Bước 3: Clone và cấu hình
```bash
# Clone repository
cd /var/www/html
sudo git clone https://github.com/yourusername/simplecms.git
cd simplecms

# Copy config
sudo cp config.sample.php config.php
sudo nano config.php
# Sửa thông tin database

# Phân quyền
sudo chown -R www-data:www-data /var/www/html/simplecms
sudo chmod -R 755 /var/www/html/simplecms
sudo chmod -R 775 /var/www/html/simplecms/uploads
```

### Bước 4: Cấu hình Apache
```bash
sudo nano /etc/apache2/sites-available/simplecms.conf
```

Thêm:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/simplecms
    
    <Directory /var/www/html/simplecms>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/simplecms_error.log
    CustomLog ${APACHE_LOG_DIR}/simplecms_access.log combined
</VirtualHost>
```
```bash
# Enable site
sudo a2ensite simplecms.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Bước 5: Tạo bảng database
```bash
sudo mysql -u simplecms_user -p simplecms
```

Chạy SQL từ file hoặc tạo thủ công các bảng như trong `install.php`

### Bước 6: Tạo admin user
```bash
sudo mysql -u simplecms_user -p simplecms

INSERT INTO users (username, email, password, role, created_at) 
VALUES ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW());
```

Password mặc định: `password`

## Cài đặt SSL (HTTPS)

### Sử dụng Let's Encrypt
```bash
# Cài Certbot
sudo apt install certbot python3-certbot-apache -y

# Lấy certificate
sudo certbot --apache -d your-domain.com

# Auto renewal
sudo certbot renew --dry-run
```

Cập nhật `config.php`:
```php
define('SITE_URL', 'https://your-domain.com');
```

## Troubleshooting

### Lỗi kết nối database
```bash
# Kiểm tra MySQL running
sudo systemctl status mysql

# Reset password MySQL
sudo mysql
ALTER USER 'simplecms_user'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;
```

### Lỗi permission denied
```bash
sudo chown -R www-data:www-data /var/www/html/simplecms
sudo chmod -R 755 /var/www/html/simplecms
sudo chmod -R 775 /var/www/html/simplecms/uploads
```

### URL rewrite không hoạt động
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Upload file lỗi
```bash
# Tăng giới hạn upload PHP
sudo nano /etc/php/8.1/apache2/php.ini

# Sửa
upload_max_filesize = 64M
post_max_size = 64M

sudo systemctl restart apache2
```

## Cài đặt trên shared hosting

1. Upload files qua FTP
2. Tạo database qua cPanel
3. Import database structure
4. Sửa `config.php`
5. Đăng nhập với admin user

## Cập nhật SimpleCMS
```bash
cd /var/www/html/simplecms
sudo chmod +x scripts/update.sh
sudo ./scripts/update.sh
```

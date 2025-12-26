# Hướng dẫn cài đặt SimpleCMS

## Phương pháp 1: Cài đặt tự động (Khuyến nghị)

### Ubuntu/Debian

```bash
# Clone repository
git clone https://github.com/thanhpham2k6/simplecms.git
cd simplecms

# Chạy script cài đặt
sudo chmod +x scripts/install.sh
sudo ./scripts/install.sh
```

Script sẽ hỏi các thông tin:
- Domain name (mặc định: localhost)
- Database name (mặc định: simplecms)
- Database user (mặc định: simplecms_user)
- Database password

### Hoàn tất cài đặt

1. Mở trình duyệt: `http://your-domain.com/install.php`
2. Điền thông tin admin
3. Click "Cài đặt SimpleCMS"
4. Đăng nhập vào admin panel

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

### Bước 5: Hoàn tất

Truy cập `http://your-domain.com/install.php`

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

Kiểm tra `.htaccess` có trong thư mục root

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
3. Sửa `config.php`
4. Truy cập `yourdomain.com/install.php`

## Cập nhật SimpleCMS

```bash
cd /var/www/html/simplecms
sudo git pull origin main
sudo chown -R www-data:www-data .
```

# Hướng dẫn cấu hình SimpleCMS

## Cấu hình cơ bản

### File config.php

File `config.php` chứa tất cả các cấu hình chính của SimpleCMS.
```php
// Database Configuration
define('DB_HOST', 'localhost');          // MySQL host
define('DB_NAME', 'simplecms');          // Tên database
define('DB_USER', 'simplecms_user');     // Username MySQL
define('DB_PASS', 'your_password');      // Password MySQL
define('DB_CHARSET', 'utf8mb4');         // Charset

// Site Configuration
define('SITE_URL', 'http://localhost');  // URL website
define('SITE_NAME', 'SimpleCMS');        // Tên website
define('SITE_DESC', 'Open Source CMS');  // Mô tả website

// Security Keys
define('AUTH_KEY', 'your-unique-key');   // Key bảo mật
define('SECURE_AUTH_KEY', 'your-key');   // Key bảo mật thứ 2
```

### Tạo Security Keys

Sử dụng lệnh sau để tạo random keys:
```bash
openssl rand -base64 32
```

## Cấu hình nâng cao

### PHP Settings

Chỉnh sửa `/etc/php/8.1/apache2/php.ini`:
```ini
# Upload
upload_max_filesize = 64M
post_max_size = 64M
max_file_uploads = 20

# Performance
max_execution_time = 300
memory_limit = 256M

# Session
session.gc_maxlifetime = 1440
session.cookie_lifetime = 0
session.cookie_httponly = 1
```

### Apache Settings

#### Enable Required Modules
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod expires
sudo a2enmod deflate
sudo systemctl restart apache2
```

#### Virtual Host Configuration
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/html/simplecms
    
    <Directory /var/www/html/simplecms>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security Headers
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/simplecms_error.log
    CustomLog ${APACHE_LOG_DIR}/simplecms_access.log combined
    
    # Compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css application/javascript
    </IfModule>
</VirtualHost>
```

### MySQL Optimization
```sql
# Tối ưu database
OPTIMIZE TABLE posts;
OPTIMIZE TABLE users;
OPTIMIZE TABLE categories;

# Backup definition
mysqldump -u simplecms_user -p simplecms > backup.sql

# Restore
mysql -u simplecms_user -p simplecms < backup.sql
```

## Cấu hình bảo mật

### File Permissions
```bash
# Owner
sudo chown -R www-data:www-data /var/www/html/simplecms

# Directories: 755
sudo find /var/www/html/simplecms -type d -exec chmod 755 {} \;

# Files: 644
sudo find /var/www/html/simplecms -type f -exec chmod 644 {} \;

# Writable directories: 775
sudo chmod -R 775 /var/www/html/simplecms/uploads
sudo chmod -R 775 /var/www/html/simplecms/cache
```

### Protect Config File
```bash
sudo chmod 600 /var/www/html/simplecms/config.php
```

### Firewall Configuration
```bash
# UFW
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### SSL/TLS Configuration
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/simplecms
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
    
    # Strong SSL
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite HIGH:!aNULL:!MD5
    SSLHonorCipherOrder on
</VirtualHost>
```

## Backup Configuration

### Automated Backup

Thêm vào crontab:
```bash
crontab -e

# Daily backup at 2 AM
0 2 * * * /var/www/html/simplecms/scripts/backup.sh >> /var/log/simplecms_backup.log 2>&1

# Weekly backup at Sunday 3 AM
0 3 * * 0 /var/www/html/simplecms/scripts/backup.sh >> /var/log/simplecms_backup.log 2>&1
```

### Backup to Remote Server
```bash
#!/bin/bash
# Add to backup.sh

# Rsync to remote
rsync -avz /var/backups/simplecms/ user@remote-server:/backups/simplecms/
```

## Performance Optimization

### Enable Caching
```apache
# .htaccess
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### Enable Gzip Compression
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>
```

## Monitoring

### Log Files
```bash
# Apache logs
tail -f /var/log/apache2/simplecms_error.log
tail -f /var/log/apache2/simplecms_access.log

# MySQL logs
tail -f /var/log/mysql/error.log

# PHP logs
tail -f /var/log/php8.1-fpm.log
```

### Disk Space Monitoring
```bash
# Check disk usage
df -h

# Check SimpleCMS size
du -sh /var/www/html/simplecms
du -sh /var/backups/simplecms
```

## Email Configuration

Để gửi email từ SimpleCMS, cấu hình SMTP:
```php
// Thêm vào config.php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_FROM', 'noreply@yourdomain.com');
```

## Multi-site Configuration

Để chạy nhiều site:
```bash
# Copy site
sudo cp -r /var/www/html/simplecms /var/www/html/site2

# Create new database
sudo mysql
CREATE DATABASE site2_db;
GRANT ALL ON site2_db.* TO 'site2_user'@'localhost' IDENTIFIED BY 'password';

# Update config.php for site2
# Create new virtual host
```

## Debug Mode

Bật debug khi development:
```php
// config.php
define('DEBUG_MODE', true);

// Sẽ hiển thị tất cả PHP errors
```

**Lưu ý:** Tắt DEBUG_MODE trên production!

<?php
require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Tạo database
        $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->exec("USE " . DB_NAME);
        
        // Tạo bảng users
        $conn->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'editor', 'author', 'subscriber') DEFAULT 'subscriber',
            created_at DATETIME NOT NULL,
            INDEX idx_username (username),
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Tạo bảng posts
        $conn->exec("CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            content LONGTEXT NOT NULL,
            excerpt TEXT,
            author_id INT NOT NULL,
            status ENUM('draft', 'published', 'private') DEFAULT 'draft',
            featured_image VARCHAR(255),
            created_at DATETIME NOT NULL,
            updated_at DATETIME,
            FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_slug (slug),
            INDEX idx_status (status),
            INDEX idx_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Tạo bảng categories
        $conn->exec("CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            INDEX idx_slug (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Tạo bảng post_categories
        $conn->exec("CREATE TABLE IF NOT EXISTS post_categories (
            post_id INT,
            category_id INT,
            PRIMARY KEY (post_id, category_id),
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
        
        // Tạo bảng settings
        $conn->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            option_name VARCHAR(100) UNIQUE NOT NULL,
            option_value LONGTEXT,
            INDEX idx_name (option_name)
        ) ENGINE=InnoDB");
        
        // Tạo admin user
        $admin_pass = password_hash($_POST['admin_password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) 
                               VALUES (:username, :email, :password, 'admin', NOW())");
        $stmt->execute([
            ':username' => $_POST['admin_username'],
            ':email' => $_POST['admin_email'],
            ':password' => $admin_pass
        ]);
        
        // Tạo thư mục uploads
        if(!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }
        
        echo "<script>alert('Cài đặt thành công!'); window.location.href='admin';</script>";
        
    } catch(PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimpleCMS - Cài đặt</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; background: #f0f0f1; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.13); }
        h1 { text-align: center; color: #1d2327; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #1d2327; font-weight: 600; }
        input { width: 100%; padding: 10px; border: 1px solid #dcdcde; border-radius: 4px; font-size: 14px; }
        input:focus { outline: none; border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
        button { width: 100%; padding: 12px; background: #2271b1; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600; }
        button:hover { background: #135e96; }
        .info { background: #f0f6fc; padding: 15px; border-left: 4px solid #2271b1; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>SimpleCMS Installation</h1>
        <div class="info">
            Chào mừng bạn đến với SimpleCMS! Vui lòng điền thông tin để hoàn tất cài đặt.
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập Admin</label>
                <input type="text" name="admin_username" required>
            </div>
            <div class="form-group">
                <label>Email Admin</label>
                <input type="email" name="admin_email" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu Admin</label>
                <input type="password" name="admin_password" required minlength="6">
            </div>
            <button type="submit">Cài đặt SimpleCMS</button>
        </form>
    </div>
</body>
</html>

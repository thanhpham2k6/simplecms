<?php
/**
 * New Secure Login Page for SimpleCMS
 * Sử dụng SecurityNew class
 * 
 * File này là phiên bản MỚI, không ảnh hưởng login.php cũ
 * Sau khi test OK, có thể thay thế login.php cũ
 */

require_once 'includes/SecurityNew.php';

// Cấu hình session bảo mật
SecurityNew::configureSession();

// Kết nối database
require_once 'config.php';

$error = '';
$success = '';

// Xử lý logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login_new.php?msg=logged_out');
    exit;
}

// Hiển thị thông báo
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'logged_out':
            $success = 'Đăng xuất thành công!';
            break;
        case 'session_expired':
            $error = 'Phiên làm việc đã hết hạn. Vui lòng đăng nhập lại.';
            break;
    }
}

// Xử lý form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !SecurityNew::validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
        SecurityNew::logSecurityEvent('csrf_validation_failed', [
            'ip' => SecurityNew::getClientIP()
        ]);
    } else {
        
        // Sanitize inputs
        $username = SecurityNew::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate inputs
        if (empty($username) || empty($password)) {
            $error = 'Vui lòng nhập đầy đủ thông tin!';
        } else {
            
            // Check rate limiting
            $rate_limit = SecurityNew::checkRateLimit($username);
            
            if (!$rate_limit['allowed']) {
                $wait_minutes = ceil($rate_limit['wait_seconds'] / 60);
                $error = "Quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau {$wait_minutes} phút.";
                
                SecurityNew::logSecurityEvent('rate_limit_exceeded', [
                    'username' => $username,
                    'ip' => SecurityNew::getClientIP()
                ]);
                
            } else {
                
                // Query user từ database (sử dụng prepared statement)
                $query = "SELECT id, username, password, email, role FROM users WHERE username = ? LIMIT 1";
                $result = SecurityNew::executeQuery($conn, $query, [$username], 's');
                
                if ($result && $result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    
                    // Verify password
                    if (SecurityNew::verifyPassword($password, $user['password'])) {
                        
                        // Login thành công
                        SecurityNew::resetRateLimit($username);
                        
                        // Regenerate session ID
                        session_regenerate_id(true);
                        
                        // Lưu thông tin user vào session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['login_time'] = time();
                        $_SESSION['last_activity'] = time();
                        
                        // Log successful login
                        SecurityNew::logSecurityEvent('login_success', [
                            'user_id' => $user['id'],
                            'username' => $user['username']
                        ]);
                        
                        // Redirect to admin
                        header('Location: admin/index.php');
                        exit;
                        
                    } else {
                        // Sai password
                        SecurityNew::recordFailedAttempt($username);
                        $remaining = $rate_limit['remaining'] - 1;
                        $error = "Tên đăng nhập hoặc mật khẩu không đúng! (Còn {$remaining} lần thử)";
                        
                        SecurityNew::logSecurityEvent('login_failed', [
                            'username' => $username,
                            'reason' => 'invalid_password'
                        ]);
                    }
                    
                } else {
                    // User không tồn tại
                    SecurityNew::recordFailedAttempt($username);
                    $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
                    
                    SecurityNew::logSecurityEvent('login_failed', [
                        'username' => $username,
                        'reason' => 'user_not_found'
                    ]);
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SimpleCMS (New Secure)</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        
        .version-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 13px;
            color: #666;
        }
        
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin-top: 15px;
            font-size: 12px;
            color: #999;
        }
        
        .security-badge svg {
            width: 16px;
            height: 16px;
        }
        
        .test-note {
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>
                SimpleCMS
                <span class="version-badge">NEW</span>
            </h1>
            <p>Đăng nhập với bảo mật nâng cao</p>
        </div>
        
        <div class="test-note">
            ℹ️ Đây là phiên bản login MỚI với SecurityNew class. Sau khi test OK sẽ thay thế login.php cũ.
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login_new.php">
            <?php echo SecurityNew::getCSRFInput(); ?>
            
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autocomplete="username"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    autocomplete="current-password"
                >
            </div>
            
            <button type="submit" class="btn-login">Đăng nhập</button>
        </form>
        
        <div class="login-footer">
            <p>SimpleCMS v1.0.0 - Secure Edition</p>
            <div class="security-badge">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Protected by SecurityNew • CSRF • Rate Limiting
            </div>
        </div>
    </div>
</body>
</html>
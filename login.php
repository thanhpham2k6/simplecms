<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';

if(is_logged_in()) {
    redirect('admin/index.php');
}

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = new User();
    if($user->login($_POST['username'], $_POST['password'])) {
        redirect('admin/index.php');
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo SITE_NAME; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f0f0f1; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-container { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.13); width: 100%; max-width: 400px; }
        h1 { text-align: center; margin-bottom: 30px; color: #1d2327; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #1d2327; }
        input { width: 100%; padding: 12px; border: 1px solid #dcdcde; border-radius: 4px; font-size: 14px; }
        input:focus { outline: none; border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }
        button { width: 100%; padding: 12px; background: #2271b1; color: white; border: none; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; }
        button:hover { background: #135e96; }
        .error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #2271b1; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Đăng nhập</h1>
        
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Tên đăng nhập hoặc Email</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Đăng nhập</button>
        </form>
        
        <div class="back-link">
            <a href="/">← Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>

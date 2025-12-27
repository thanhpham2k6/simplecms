<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Kiểm tra và load config
$config_file = __DIR__ . '/../config.php';
if (!file_exists($config_file)) {
    die('
    <div style="font-family: Arial; padding: 40px; background: #fff3cd; border-left: 4px solid #ffc107;">
        <h2>⚠️ Lỗi: Thiếu file cấu hình</h2>
        <p><strong>File config.php không tồn tại!</strong></p>
        <p>Vui lòng thực hiện 1 trong 2 cách sau:</p>
        <ol>
            <li>Chạy trình cài đặt: <a href="../install.php">install.php</a></li>
            <li>Copy file config.sample.php thành config.php và cấu hình thông tin database</li>
        </ol>
    </div>
    ');
}

require_once $config_file;

// Kiểm tra các hằng số cần thiết
if (!defined('INCLUDES_PATH')) {
    die('ERROR: INCLUDES_PATH không được định nghĩa trong config.php');
}

// Kiểm tra và load các file includes
$required_files = [
    'database.php' => INCLUDES_PATH . '/database.php',
    'functions.php' => INCLUDES_PATH . '/functions.php',
    'user.class.php' => INCLUDES_PATH . '/user.class.php',
    'post.class.php' => INCLUDES_PATH . '/post.class.php'
];

foreach ($required_files as $name => $path) {
    if (!file_exists($path)) {
        die("ERROR: File {$name} không tồn tại tại đường dẫn: {$path}");
    }
    require_once $path;
}

// Kiểm tra đăng nhập
if (!function_exists('is_logged_in')) {
    die('ERROR: Hàm is_logged_in() không tồn tại. Kiểm tra file functions.php');
}

if (!is_logged_in()) {
    redirect('../login.php');
    exit;
}

// Khởi tạo các đối tượng với error handling
try {
    $post = new Post();
    $user = new User();
    
    // Lấy thống kê
    $total_posts = $post->count();
    $published_posts = $post->count('published');
    $draft_posts = $post->count('draft');
    $recent_posts = $post->getAll(null, 5);
    
} catch (Exception $e) {
    die('
    <div style="font-family: Arial; padding: 40px; background: #f8d7da; border-left: 4px solid #dc3545;">
        <h2>🔴 Lỗi Database</h2>
        <p><strong>Không thể kết nối hoặc truy vấn database!</strong></p>
        <p>Chi tiết lỗi: ' . htmlspecialchars($e->getMessage()) . '</p>
        <hr>
        <p><strong>Các bước kiểm tra:</strong></p>
        <ol>
            <li>Kiểm tra thông tin database trong config.php</li>
            <li>Đảm bảo MySQL/MariaDB đang chạy</li>
            <li>Kiểm tra user có quyền truy cập database</li>
            <li>Chạy lại install.php nếu chưa cài đặt database</li>
        </ol>
    </div>
    ');
}

// Include header
$header_file = __DIR__ . '/header.php';
if (file_exists($header_file)) {
    include $header_file;
} else {
    echo '<p style="color: red;">WARNING: header.php không tồn tại</p>';
}
?>

<div class="dashboard">
    <h1>Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Tổng bài viết</h3>
            <p class="stat-number"><?php echo $total_posts; ?></p>
        </div>
        <div class="stat-card">
            <h3>Đã xuất bản</h3>
            <p class="stat-number"><?php echo $published_posts; ?></p>
        </div>
        <div class="stat-card">
            <h3>Bản nháp</h3>
            <p class="stat-number"><?php echo $draft_posts; ?></p>
        </div>
    </div>
    
    <div class="recent-posts">
        <h2>Bài viết gần đây</h2>
        <table class="wp-table">
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Tác giả</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($recent_posts)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px;">
                        <p>📝 Chưa có bài viết nào</p>
                        <a href="post-new.php" class="button">Tạo bài viết đầu tiên</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach($recent_posts as $p): ?>
                    <tr>
                        <td><strong><?php echo sanitize_text($p['title']); ?></strong></td>
                        <td><?php echo sanitize_text($p['author_name']); ?></td>
                        <td><span class="status-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td><?php echo format_date($p['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
// Include footer
$footer_file = __DIR__ . '/footer.php';
if (file_exists($footer_file)) {
    include $footer_file;
} else {
    echo '<p style="color: red;">WARNING: footer.php không tồn tại</p>';
}
?>

<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';

if(!is_logged_in() || !is_admin()) {
    redirect('index.php');
}

$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý lưu cài đặt (có thể mở rộng thêm)
    $message = '<div class="success">Cài đặt đã được lưu!</div>';
}

include 'header.php';
?>

<div class="settings-page">
    <h1>Cài đặt</h1>
    
    <?php echo $message; ?>
    
    <div class="box">
        <form method="POST">
            <div class="form-group">
                <label>Tên website</label>
                <input type="text" name="site_name" value="<?php echo SITE_NAME; ?>">
            </div>
            
            <div class="form-group">
                <label>Mô tả website</label>
                <textarea name="site_desc" style="min-height: 100px;"><?php echo SITE_DESC; ?></textarea>
            </div>
            
            <div class="form-group">
                <label>URL website</label>
                <input type="url" name="site_url" value="<?php echo SITE_URL; ?>">
            </div>
            
            <button type="submit" class="btn">Lưu cài đặt</button>
        </form>
    </div>
    
    <div style="margin-top: 30px; background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107;">
        <h3>⚠️ Lưu ý</h3>
        <p>Để thay đổi cài đặt này, bạn cần chỉnh sửa file <code>config.php</code> trực tiếp.</p>
    </div>
</div>

<?php include 'footer.php'; ?>

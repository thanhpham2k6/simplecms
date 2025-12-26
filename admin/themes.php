<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';
require_once INCLUDES_PATH . '/theme.class.php';

if(!is_logged_in() || !is_admin()) {
    redirect('index.php');
}

$theme = new Theme();
$message = '';

// Xử lý kích hoạt theme
if(isset($_GET['action']) && $_GET['action'] === 'activate' && isset($_GET['theme'])) {
    $theme->setActiveTheme($_GET['theme']);
    $message = '<div class="success">Theme đã được kích hoạt!</div>';
}

$themes = $theme->getAll();

include 'header.php';
?>

<div class="themes-page">
    <h1>Quản lý giao diện</h1>
    
    <?php echo $message; ?>
    
    <table class="wp-table">
        <thead>
            <tr>
                <th>Tên Theme</th>
                <th>Trạng thái</th>
                <th style="width: 150px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($themes)): ?>
            <tr>
                <td colspan="3" style="text-align: center; padding: 30px;">Chưa có theme nào được cài đặt</td>
            </tr>
            <?php else: ?>
                <?php foreach($themes as $t): ?>
                <tr>
                    <td><strong><?php echo sanitize_text($t['name']); ?></strong></td>
                    <td>
                        <?php if($t['active']): ?>
                            <span style="color: #00a32a; font-weight: 600;">Active</span>
                        <?php else: ?>
                            <span style="color: #646970;">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(!$t['active']): ?>
                            <a href="themes.php?action=activate&theme=<?php echo $t['name']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Kích hoạt</a>
                        <?php else: ?>
                            <span style="color: #646970;">Đang sử dụng</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; background: #f0f6fc; padding: 20px; border-radius: 8px; border-left: 4px solid #2271b1;">
        <h3>Cách tạo Theme</h3>
        <p>Tạo thư mục mới trong <code>/themes/your-theme-name/</code> với các file:</p>
        <ul style="margin: 10px 0 0 20px;">
            <li>header.php - Header template</li>
            <li>footer.php - Footer template</li>
            <li>single.php - Single post template</li>
            <li>style.css - Theme styles</li>
        </ul>
    </div>
</div>

<?php include 'footer.php'; ?>

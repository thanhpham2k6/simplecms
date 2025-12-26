<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';
require_once INCLUDES_PATH . '/plugin.class.php';

if(!is_logged_in() || !is_admin()) {
    redirect('index.php');
}

$plugin = new Plugin();
$message = '';

// Xử lý activate/deactivate
if(isset($_GET['action']) && isset($_GET['plugin'])) {
    if($_GET['action'] === 'activate') {
        $plugin->activate($_GET['plugin']);
        $message = '<div class="success">Plugin đã được kích hoạt!</div>';
    } elseif($_GET['action'] === 'deactivate') {
        $plugin->deactivate($_GET['plugin']);
        $message = '<div class="success">Plugin đã bị tắt!</div>';
    }
}

$plugins = $plugin->getAll();

include 'header.php';
?>

<div class="plugins-page">
    <h1>Quản lý Plugin</h1>
    
    <?php echo $message; ?>
    
    <table class="wp-table">
        <thead>
            <tr>
                <th>Tên Plugin</th>
                <th>Trạng thái</th>
                <th style="width: 150px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($plugins)): ?>
            <tr>
                <td colspan="3" style="text-align: center; padding: 30px;">Chưa có plugin nào được cài đặt</td>
            </tr>
            <?php else: ?>
                <?php foreach($plugins as $p): ?>
                <tr>
                    <td><strong><?php echo sanitize_text($p['name']); ?></strong></td>
                    <td>
                        <?php if($p['active']): ?>
                            <span style="color: #00a32a; font-weight: 600;">Active</span>
                        <?php else: ?>
                            <span style="color: #646970;">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($p['active']): ?>
                            <a href="plugins.php?action=deactivate&plugin=<?php echo $p['name']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">Deactivate</a>
                        <?php else: ?>
                            <a href="plugins.php?action=activate&plugin=<?php echo $p['name']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div style="margin-top: 30px; background: #f0f6fc; padding: 20px; border-radius: 8px; border-left: 4px solid #2271b1;">
        <h3>Cách tạo Plugin</h3>
        <p>Tạo thư mục mới trong <code>/plugins/your-plugin-name/</code> và tạo file <code>plugin.php</code></p>
        <pre style="background: white; padding: 15px; border-radius: 4px; margin-top: 10px; overflow-x: auto;">
&lt;?php
/*
Plugin Name: My Custom Plugin
Description: Description of my plugin
Version: 1.0
Author: Your Name
*/

// Your plugin code here
function my_plugin_activate() {
    // Code khi activate plugin
}

function my_plugin_deactivate() {
    // Code khi deactivate plugin
}
?&gt;
        </pre>
    </div>
</div>

<?php include 'footer.php'; ?>

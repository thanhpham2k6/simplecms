<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';

if(!is_logged_in() || !is_admin()) {
    redirect('index.php');
}

$user = new User();
$users = $user->getAll();

include 'header.php';
?>

<div class="users-page">
    <h1>Quản lý người dùng</h1>
    
    <table class="wp-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($users)): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 30px;">Chưa có người dùng nào</td>
            </tr>
            <?php else: ?>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo sanitize_text($u['username']); ?></td>
                    <td><?php echo sanitize_text($u['email']); ?></td>
                    <td><?php echo ucfirst($u['role']); ?></td>
                    <td><?php echo format_date($u['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>

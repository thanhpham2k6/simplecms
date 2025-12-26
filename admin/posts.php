<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';
require_once INCLUDES_PATH . '/post.class.php';

if(!is_logged_in()) {
    redirect('../login.php');
}

$post = new Post();
$posts = $post->getAll();

// Xử lý xóa bài viết
if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $post->delete($_GET['id']);
    redirect('posts.php');
}

include 'header.php';
?>

<div class="posts-page">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Quản lý bài viết</h1>
        <a href="new-post.php" class="btn">Thêm bài viết mới</a>
    </div>
    
    <table class="wp-table">
        <thead>
            <tr>
                <th style="width: 50px;">ID</th>
                <th>Tiêu đề</th>
                <th>Tác giả</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th style="width: 150px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($posts)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 30px;">Chưa có bài viết nào</td>
            </tr>
            <?php else: ?>
                <?php foreach($posts as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td>
                        <strong><?php echo sanitize_text($p['title']); ?></strong>
                    </td>
                    <td><?php echo sanitize_text($p['author_name']); ?></td>
                    <td><span class="status-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span></td>
                    <td><?php echo format_date($p['created_at']); ?></td>
                    <td>
                        <a href="edit-post.php?id=<?php echo $p['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Sửa</a>
                        <a href="posts.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>

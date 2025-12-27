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
$user = new User();

$total_posts = $post->count();
$published_posts = $post->count('published');
$draft_posts = $post->count('draft');
$recent_posts = $post->getAll(null, 5);

include 'header.php';
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
                    <td colspan="4" style="text-align: center; padding: 30px;">Chưa có bài viết nào</td>
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

<?php include 'footer.php'; ?>

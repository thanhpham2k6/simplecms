<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';
require_once INCLUDES_PATH . '/post.class.php';

if(!is_logged_in()) {
    redirect('../login.php');
}

$postObj = new Post();
$message = '';

if(!isset($_GET['id'])) {
    redirect('posts.php');
}

$post_data = $postObj->getById($_GET['id']);

if(!$post_data) {
    redirect('posts.php');
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $featured_image = $post_data['featured_image'];
    if(isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $featured_image = upload_file($_FILES['featured_image']);
    }
    
    $data = [
        'title' => $_POST['title'],
        'slug' => create_slug($_POST['title']),
        'content' => $_POST['content'],
        'excerpt' => $_POST['excerpt'],
        'status' => $_POST['status'],
        'featured_image' => $featured_image
    ];
    
    if($postObj->update($_GET['id'], $data)) {
        $message = '<div class="success">Bài viết đã được cập nhật!</div>';
        $post_data = $postObj->getById($_GET['id']);
    }
}

include 'header.php';
?>

<div class="edit-post-page">
    <h1>Chỉnh sửa bài viết</h1>
    
    <?php echo $message; ?>
    
    <form method="POST" enctype="multipart/form-data" class="box">
        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post_data['title']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Nội dung</label>
            <textarea name="content" required><?php echo htmlspecialchars($post_data['content']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Tóm tắt</label>
            <textarea name="excerpt" style="min-height: 100px;"><?php echo htmlspecialchars($post_data['excerpt']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Ảnh đại diện</label>
            <?php if($post_data['featured_image']): ?>
                <div style="margin-bottom: 10px;">
                    <img src="<?php echo SITE_URL . $post_data['featured_image']; ?>" style="max-width: 200px; height: auto;">
                </div>
            <?php endif; ?>
            <input type="file" name="featured_image" accept="image/*">
        </div>
        
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status">
                <option value="draft" <?php echo $post_data['status'] === 'draft' ? 'selected' : ''; ?>>Bản nháp</option>
                <option value="published" <?php echo $post_data['status'] === 'published' ? 'selected' : ''; ?>>Xuất bản</option>
                <option value="private" <?php echo $post_data['status'] === 'private' ? 'selected' : ''; ?>>Riêng tư</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Cập nhật</button>
        <a href="posts.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

<?php include 'footer.php'; ?>

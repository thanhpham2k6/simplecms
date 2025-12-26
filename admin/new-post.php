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
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $featured_image = null;
    if(isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $featured_image = upload_file($_FILES['featured_image']);
    }
    
    $data = [
        'title' => $_POST['title'],
        'slug' => create_slug($_POST['title']),
        'content' => $_POST['content'],
        'excerpt' => $_POST['excerpt'],
        'author_id' => $_SESSION['user_id'],
        'status' => $_POST['status'],
        'featured_image' => $featured_image
    ];
    
    if($post->create($data)) {
        $message = '<div class="success">Bài viết đã được tạo thành công!</div>';
    }
}

include 'header.php';
?>

<div class="new-post-page">
    <h1>Thêm bài viết mới</h1>
    
    <?php echo $message; ?>
    
    <form method="POST" enctype="multipart/form-data" class="box">
        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" name="title" required>
        </div>
        
        <div class="form-group">
            <label>Nội dung</label>
            <textarea name="content" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Tóm tắt</label>
            <textarea name="excerpt" style="min-height: 100px;"></textarea>
        </div>
        
        <div class="form-group">
            <label>Ảnh đại diện</label>
            <input type="file" name="featured_image" accept="image/*">
        </div>
        
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status">
                <option value="draft">Bản nháp</option>
                <option value="published">Xuất bản</option>
                <option value="private">Riêng tư</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Tạo bài viết</button>
        <a href="posts.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include 'footer.php'; ?>

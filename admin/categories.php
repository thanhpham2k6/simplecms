<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/user.class.php';
require_once INCLUDES_PATH . '/category.class.php';

if(!is_logged_in()) {
    redirect('../login.php');
}

$category = new Category();
$message = '';

// Xử lý tạo danh mục mới
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = $_POST['name'];
    $slug = create_slug($name);
    $description = $_POST['description'];
    
    if($category->create($name, $slug, $description)) {
        $message = '<div class="success">Danh mục đã được tạo!</div>';
    }
}

// Xử lý xóa danh mục
if(isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $category->delete($_GET['id']);
    redirect('categories.php');
}

$categories = $category->getAll();

include 'header.php';
?>

<div class="categories-page">
    <h1>Quản lý danh mục</h1>
    
    <?php echo $message; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
        <!-- Form thêm danh mục -->
        <div class="box">
            <h2>Thêm danh mục mới</h2>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label>Tên danh mục</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" style="min-height: 100px;"></textarea>
                </div>
                <button type="submit" class="btn">Thêm danh mục</button>
            </form>
        </div>
        
        <!-- Danh sách danh mục -->
        <div>
            <table class="wp-table">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Slug</th>
                        <th>Số bài viết</th>
                        <th style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($categories)): ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px;">Chưa có danh mục nào</td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td><strong><?php echo sanitize_text($cat['name']); ?></strong></td>
                            <td><?php echo sanitize_text($cat['slug']); ?></td>
                            <td><?php echo $cat['post_count']; ?></td>
                            <td>
                                <a href="categories.php?action=delete&id=<?php echo $cat['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

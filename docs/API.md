# SimpleCMS API Documentation

## Giới thiệu

SimpleCMS cung cấp một số classes và functions có thể sử dụng để phát triển themes và plugins.

## Core Classes

### Database Class

Singleton class để quản lý kết nối database.

#### Methods
```php
// Get instance
$db = Database::getInstance();

// Get connection
$conn = $db->getConnection();

// Execute query
$stmt = $db->query($sql, $params);
```

#### Example
```php
$db = Database::getInstance();
$conn = $db->getConnection();

$sql = "SELECT * FROM posts WHERE status = :status";
$stmt = $conn->prepare($sql);
$stmt->execute([':status' => 'published']);
$posts = $stmt->fetchAll();
```

---

### Post Class

Quản lý bài viết.

#### Methods

##### create($data)

Tạo bài viết mới.
```php
$post = new Post();
$result = $post->create([
    'title' => 'My Post',
    'slug' => 'my-post',
    'content' => 'Post content...',
    'excerpt' => 'Short description',
    'author_id' => 1,
    'status' => 'published',
    'featured_image' => '/uploads/image.jpg'
]);
```

##### getAll($status, $limit, $offset)

Lấy danh sách bài viết.
```php
$posts = $post->getAll('published', 10, 0);
// Returns: array of posts
```

##### getById($id)

Lấy bài viết theo ID.
```php
$post_data = $post->getById(1);
// Returns: array with post data or false
```

##### getBySlug($slug)

Lấy bài viết theo slug.
```php
$post_data = $post->getBySlug('my-post');
```

##### update($id, $data)

Cập nhật bài viết.
```php
$post->update(1, [
    'title' => 'Updated Title',
    'content' => 'Updated content',
    'status' => 'published'
]);
```

##### delete($id)

Xóa bài viết.
```php
$post->delete(1);
```

##### count($status)

Đếm số bài viết.
```php
$total = $post->count('published');
// Returns: integer
```

---

### User Class

Quản lý người dùng.

#### Methods

##### register($username, $email, $password, $role)

Đăng ký user mới.
```php
$user = new User();
$result = $user->register('john', 'john@example.com', 'password123', 'subscriber');
```

##### login($username, $password)

Đăng nhập.
```php
if ($user->login('john', 'password123')) {
    echo "Login successful";
}
```

##### logout()

Đăng xuất.
```php
$user->logout();
```

##### getById($id)

Lấy thông tin user.
```php
$user_data = $user->getById(1);
```

##### getAll()

Lấy tất cả users.
```php
$users = $user->getAll();
```

##### update($id, $data)

Cập nhật user.
```php
$user->update(1, [
    'email' => 'newemail@example.com',
    'role' => 'editor'
]);
```

##### delete($id)

Xóa user.
```php
$user->delete(1);
```

---

### Category Class

Quản lý danh mục.

#### Methods

##### create($name, $slug, $description)

Tạo danh mục mới.
```php
$category = new Category();
$category->create('Technology', 'technology', 'Tech articles');
```

##### getAll()

Lấy tất cả danh mục.
```php
$categories = $category->getAll();
```

##### getById($id)

Lấy danh mục theo ID.
```php
$cat = $category->getById(1);
```

##### update($id, $name, $slug, $description)

Cập nhật danh mục.
```php
$category->update(1, 'Tech News', 'tech-news', 'Latest tech news');
```

##### delete($id)

Xóa danh mục.
```php
$category->delete(1);
```

##### addPostCategory($post_id, $category_id)

Gán danh mục cho bài viết.
```php
$category->addPostCategory(1, 2);
```

---

### Theme Class

Quản lý giao diện.

#### Methods

##### getActiveTheme()

Lấy theme đang active.
```php
$theme = new Theme();
$active = $theme->getActiveTheme();
// Returns: string (theme name)
```

##### setActiveTheme($theme_name)

Kích hoạt theme.
```php
$theme->setActiveTheme('my-theme');
```

##### getAll()

Lấy tất cả themes.
```php
$themes = $theme->getAll();
// Returns: array of themes with 'name', 'active', 'path'
```

##### getTemplatePath($template)

Lấy đường dẫn template.
```php
$path = $theme->getTemplatePath('header.php');
include $path;
```

---

### Plugin Class

Quản lý plugins.

#### Methods

##### activate($plugin_name)

Kích hoạt plugin.
```php
$plugin = new Plugin();
$plugin->activate('my-plugin');
```

##### deactivate($plugin_name)

Tắt plugin.
```php
$plugin->deactivate('my-plugin');
```

##### getAll()

Lấy tất cả plugins.
```php
$plugins = $plugin->getAll();
```

##### loadPlugins()

Load tất cả active plugins.
```php
$plugin->loadPlugins();
```

---

## Helper Functions

### sanitize_text($text)

Làm sạch text output.
```php
echo sanitize_text($user_input);
```

### sanitize_email($email)

Validate và làm sạch email.
```php
$clean_email = sanitize_email($_POST['email']);
```

### is_logged_in()

Kiểm tra user đã đăng nhập.
```php
if (is_logged_in()) {
    // User is logged in
}
```

### is_admin()

Kiểm tra user có quyền admin.
```php
if (is_admin()) {
    // User is admin
}
```

### redirect($url)

Redirect đến URL.
```php
redirect('/admin/posts.php');
```

### get_current_user()

Lấy thông tin user hiện tại.
```php
$user = get_current_user();
echo $user['username'];
```

### format_date($date)

Format ngày tháng.
```php
echo format_date('2024-01-15 10:30:00');
// Output: 15/01/2024 10:30
```

### create_slug($text)

Tạo slug từ text.
```php
$slug = create_slug('Bài viết của tôi');
// Output: bai-viet-cua-toi
```

### upload_file($file, $allowed_types)

Upload file.
```php
$path = upload_file($_FILES['image'], ['image/jpeg', 'image/png']);
if ($path) {
    echo "Uploaded to: " . $path;
}
```

---

## Security Functions

### generate_csrf_token()

Tạo CSRF token.
```php
$token = generate_csrf_token();
```

### verify_csrf_token($token)

Verify CSRF token.
```php
if (verify_csrf_token($_POST['csrf_token'])) {
    // Valid token
}
```

### xss_clean($data)

Clean XSS.
```php
$clean = xss_clean($user_input);
```

### check_rate_limit($identifier, $max_attempts, $time_window)

Rate limiting.
```php
if (!check_rate_limit('login_' . $username, 5, 900)) {
    die('Too many attempts');
}
```

---

## Hook System

### add_action($hook, $callback)

Thêm action hook.
```php
function my_function() {
    echo "Hook called!";
}

add_action('init', 'my_function');
```

### do_action($hook)

Thực thi action hook.
```php
do_action('init');
```

### Available Hooks

- `init` - Chạy khi khởi tạo
- `admin_menu` - Thêm admin menu
- `before_post_create` - Trước khi tạo post
- `after_post_create` - Sau khi tạo post
- `before_post_update` - Trước khi update post
- `after_post_update` - Sau khi update post

---

## Constants

### Site Constants
```php
SITE_URL      // URL của website
SITE_NAME     // Tên website
SITE_DESC     // Mô tả website
```

### Database Constants
```php
DB_HOST       // MySQL host
DB_NAME       // Database name
DB_USER       // Database user
DB_PASS       // Database password
DB_CHARSET    // Character set
```

### Path Constants
```php
ROOT_PATH       // Root directory
ADMIN_PATH      // Admin directory
INCLUDES_PATH   // Includes directory
THEMES_PATH     // Themes directory
PLUGINS_PATH    // Plugins directory
UPLOADS_PATH    // Uploads directory
```

### Security Constants
```php
AUTH_KEY              // Authentication key
SECURE_AUTH_KEY       // Secure authentication key
DEBUG_MODE            // Debug mode (true/false)
```

---

## Complete Example: Custom Plugin
```php
<?php
/*
Plugin Name: Post Views Counter
Description: Đếm số lượt xem bài viết
Version: 1.0.0
*/

// Activation
function post_views_activate() {
    $db = Database::getInstance()->getConnection();
    $db->exec("CREATE TABLE IF NOT EXISTS post_views (
        post_id INT PRIMARY KEY,
        views INT DEFAULT 0
    )");
}

// Count view
function post_views_count($post_id) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "INSERT INTO post_views (post_id, views) VALUES (?, 1) 
            ON DUPLICATE KEY UPDATE views = views + 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([$post_id]);
}

// Get views
function post_views_get($post_id) {
    $db = Database::getInstance()->getConnection();
    
    $sql = "SELECT views FROM post_views WHERE post_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$post_id]);
    $result = $stmt->fetch();
    
    return $result ? $result['views'] : 0;
}

// Hook to count views on single post
add_action('post_view', 'post_views_count');

// Usage in theme:
// $views = post_views_get($post_id);
// echo "Views: " . $views;
?>
```

---

## Error Handling

### Try-Catch Example
```php
try {
    $post = new Post();
    $result = $post->create($data);
    
    if (!$result) {
        throw new Exception('Failed to create post');
    }
    
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    echo 'An error occurred';
}
```

---

## Best Practices

1. **Always use prepared statements**
2. **Sanitize all user input**
3. **Escape all output**
4. **Use CSRF tokens for forms**
5. **Check user permissions**
6. **Log errors properly**
7. **Follow naming conventions**
8. **Document your code**

---

## Support

- GitHub Issues: https://github.com/yourusername/simplecms/issues
- Documentation: https://docs.simplecms.com
- Community Forum: https://forum.simplecms.com

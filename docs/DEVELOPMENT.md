# Hướng dẫn phát triển SimpleCMS

## Giới thiệu

SimpleCMS được xây dựng với kiến trúc MVC đơn giản và dễ mở rộng.

## Cấu trúc Code
```
simplecms/
├── config.php              # Cấu hình chính
├── index.php              # Front-end entry point
├── login.php              # Authentication
├── includes/              # Core classes
│   ├── database.php       # Database connection
│   ├── functions.php      # Helper functions
│   ├── *.class.php        # Model classes
├── admin/                 # Admin panel
├── themes/                # Theme templates
└── plugins/               # Plugin system
```

## Coding Standards

### PHP Coding Style
```php
<?php
// PSR-2 style

class MyClass {
    private $property;
    
    public function myMethod($param) {
        if ($condition) {
            // Do something
        }
        return $result;
    }
}
```

### Naming Conventions

- **Classes:** PascalCase (`Post`, `User`, `Category`)
- **Methods:** camelCase (`getById`, `createPost`)
- **Variables:** snake_case (`$post_data`, `$user_id`)
- **Constants:** UPPER_CASE (`DB_HOST`, `SITE_URL`)

## Tạo Model mới

### Bước 1: Tạo Class File

Tạo file `includes/comment.class.php`:
```php
<?php
class Comment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($post_id, $author, $content) {
        $sql = "INSERT INTO comments (post_id, author, content, created_at) 
                VALUES (:post_id, :author, :content, NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':post_id' => $post_id,
            ':author' => $author,
            ':content' => $content
        ]);
    }
    
    public function getByPostId($post_id) {
        $sql = "SELECT * FROM comments WHERE post_id = :post_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':post_id' => $post_id]);
        return $stmt->fetchAll();
    }
}
```

### Bước 2: Tạo Database Table
```sql
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    author VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### Bước 3: Sử dụng trong Controller
```php
<?php
require_once INCLUDES_PATH . '/comment.class.php';

$comment = new Comment();
$comments = $comment->getByPostId($post_id);
```

## Tạo Theme mới

### Cấu trúc Theme
```
themes/my-theme/
├── header.php          # Header template
├── footer.php          # Footer template
├── single.php          # Single post template
├── archive.php         # Archive/category template
├── style.css           # Theme styles
└── functions.php       # Theme functions (optional)
```

### Template Tags

#### header.php
```php
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title ?? SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo THEMES_PATH; ?>/my-theme/style.css">
</head>
<body>
    <header>
        <h1><?php echo SITE_NAME; ?></h1>
        <nav>
            <!-- Menu here -->
        </nav>
    </header>
```

#### footer.php
```php
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></p>
    </footer>
</body>
</html>
```

#### single.php
```php
<?php
$post = new Post();
$post_data = $post->getBySlug($_GET['slug']);
include 'header.php';
?>

<article>
    <h1><?php echo sanitize_text($post_data['title']); ?></h1>
    <div><?php echo nl2br(sanitize_text($post_data['content'])); ?></div>
</article>

<?php include 'footer.php'; ?>
```

## Tạo Plugin mới

### Cấu trúc Plugin
```
plugins/my-plugin/
├── plugin.php          # Main plugin file
├── assets/
│   ├── css/
│   └── js/
└── README.md
```

### Plugin Template
```php
<?php
/*
Plugin Name: My Custom Plugin
Description: Mô tả plugin của bạn
Version: 1.0.0
Author: Your Name
Author URI: https://yoursite.com
*/

// Prevent direct access
if (!defined('ROOT_PATH')) {
    exit;
}

// Activation hook
function my_plugin_activate() {
    // Code chạy khi plugin được kích hoạt
    // Ví dụ: tạo bảng database, thêm options, etc.
}

// Deactivation hook
function my_plugin_deactivate() {
    // Code chạy khi plugin bị tắt
    // Ví dụ: xóa temporary data, etc.
}

// Init hook
function my_plugin_init() {
    // Code chạy mỗi khi page load
}

// Admin menu hook
function my_plugin_admin_menu() {
    // Thêm menu vào admin panel
}

// Example: Add custom post meta
function my_plugin_add_meta($post_id, $key, $value) {
    $db = Database::getInstance()->getConnection();
    $sql = "INSERT INTO post_meta (post_id, meta_key, meta_value) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    return $stmt->execute([$post_id, $key, $value]);
}

// Hooks
add_action('init', 'my_plugin_init');
add_action('admin_menu', 'my_plugin_admin_menu');
```

## Helper Functions

### Thêm Helper Function

Trong `includes/functions.php`:
```php
// Get post excerpt
function get_excerpt($content, $length = 150) {
    $content = strip_tags($content);
    if (strlen($content) > $length) {
        $content = substr($content, 0, $length) . '...';
    }
    return $content;
}

// Format number
function format_number($number) {
    return number_format($number, 0, ',', '.');
}

// Time ago
function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
    
    return format_date($datetime);
}
```

## Database Queries

### Sử dụng Prepared Statements
```php
// SELECT
$sql = "SELECT * FROM posts WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $id]);
$result = $stmt->fetch();

// INSERT
$sql = "INSERT INTO posts (title, content) VALUES (:title, :content)";
$stmt = $db->prepare($sql);
$stmt->execute([':title' => $title, ':content' => $content]);

// UPDATE
$sql = "UPDATE posts SET title = :title WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([':title' => $title, ':id' => $id]);

// DELETE
$sql = "DELETE FROM posts WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $id]);
```

## Security Best Practices

### Input Validation
```php
// Sanitize text
$clean_text = sanitize_text($_POST['text']);

// Sanitize email
$clean_email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

// Sanitize URL
$clean_url = filter_var($_POST['url'], FILTER_VALIDATE_URL);

// Sanitize integer
$clean_id = (int)$_POST['id'];
```

### Output Escaping
```php
// HTML output
echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

// Or use helper
echo sanitize_text($text);

// URL output
echo urlencode($text);
```

### CSRF Protection
```php
// Generate token
$token = generate_csrf_token();

// In form
<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

// Verify
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('Invalid CSRF token');
}
```

## Testing

### Manual Testing Checklist

- [ ] Create/Edit/Delete posts
- [ ] User login/logout
- [ ] File upload
- [ ] URL rewriting
- [ ] Form validation
- [ ] Error handling
- [ ] Cross-browser testing

### Database Testing
```sql
-- Test data
INSERT INTO posts (title, slug, content, author_id, status, created_at) 
VALUES ('Test Post', 'test-post', 'Test content', 1, 'published', NOW());

-- Verify
SELECT * FROM posts WHERE slug = 'test-post';

-- Cleanup
DELETE FROM posts WHERE slug = 'test-post';
```

## Debugging

### Enable Debug Mode
```php
// config.php
define('DEBUG_MODE', true);

// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Log Debugging
```php
// Write to log
error_log('Debug message: ' . print_r($variable, true));

// View logs
tail -f /var/log/apache2/error.log
```

## Git Workflow

### Development Workflow
```bash
# Create feature branch
git checkout -b feature/my-feature

# Make changes and commit
git add .
git commit -m "Add: my new feature"

# Push to remote
git push origin feature/my-feature

# Create pull request on GitHub

# After merge, update main
git checkout main
git pull origin main
```

### Commit Message Format
```
Type: Short description

Longer description if needed.

Types:
- Add: New feature
- Fix: Bug fix
- Update: Changes to existing feature
- Remove: Remove feature
- Refactor: Code refactoring
- Docs: Documentation changes
```

## Performance Tips

### Database Optimization
```php
// Use indexes
CREATE INDEX idx_slug ON posts(slug);
CREATE INDEX idx_status ON posts(status);

// Limit queries
$posts = $post->getAll('published', 10); // LIMIT 10

// Use joins instead of multiple queries
```

### Caching
```php
// Simple file cache
function get_cached($key, $callback, $ttl = 3600) {
    $cache_file = ROOT_PATH . '/cache/' . md5($key) . '.cache';
    
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $ttl) {
        return unserialize(file_get_contents($cache_file));
    }
    
    $data = $callback();
    file_put_contents($cache_file, serialize($data));
    return $data;
}

// Usage
$posts = get_cached('recent_posts', function() use ($post) {
    return $post->getAll('published', 5);
}, 300);
```

## Deployment

### Production Checklist

- [ ] DEBUG_MODE = false
- [ ] Update SITE_URL
- [ ] Change default passwords
- [ ] Set proper file permissions
- [ ] Enable SSL/HTTPS
- [ ] Setup backups
- [ ] Configure firewall
- [ ] Test all features
- [ ] Monitor error logs

### Deploy Script
```bash
#!/bin/bash
# deploy.sh

git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
sudo chown -R www-data:www-data .
sudo systemctl restart apache2
```

## Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [OWASP Security](https://owasp.org/)

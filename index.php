<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/post.class.php';

$post = new Post();
$posts = $post->getAll('published', 10);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_DESC; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
        
        header { background: #fff; border-bottom: 1px solid #e1e4e8; }
        .header-container { max-width: 1200px; margin: 0 auto; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .site-title { font-size: 28px; font-weight: 700; color: #2271b1; text-decoration: none; }
        nav ul { display: flex; list-style: none; gap: 20px; }
        nav a { color: #333; text-decoration: none; font-weight: 500; }
        nav a:hover { color: #2271b1; }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; }
        .post-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .post-card:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .post-image { width: 100%; height: 200px; background: #f0f0f1; object-fit: cover; }
        .post-content { padding: 20px; }
        .post-title { font-size: 22px; margin-bottom: 10px; }
        .post-title a { color: #1d2327; text-decoration: none; }
        .post-title a:hover { color: #2271b1; }
        .post-meta { color: #646970; font-size: 14px; margin-bottom: 15px; }
        .post-excerpt { color: #50575e; line-height: 1.7; margin-bottom: 15px; }
        .read-more { color: #2271b1; text-decoration: none; font-weight: 600; }
        .read-more:hover { text-decoration: underline; }
        
        footer { background: #1d2327; color: #fff; padding: 40px 20px; margin-top: 60px; text-align: center; }
        
        .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 8px; }
        .empty-state h2 { color: #646970; margin-bottom: 10px; }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <a href="/" class="site-title"><?php echo SITE_NAME; ?></a>
            <nav>
                <ul>
                    <li><a href="/">Trang chủ</a></li>
                    <li><a href="/admin">Quản trị</a></li>
                    <?php if(is_logged_in()): ?>
                        <li><a href="/admin">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="/login.php">Đăng nhập</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <?php if(empty($posts)): ?>
            <div class="empty-state">
                <h2>Chào mừng đến với <?php echo SITE_NAME; ?></h2>
                <p>Chưa có bài viết nào được xuất bản.</p>
                <p><a href="/admin" style="color: #2271b1;">Đăng nhập</a> để tạo bài viết đầu tiên!</p>
            </div>
        <?php else: ?>
            <div class="posts-grid">
                <?php foreach($posts as $p): ?>
                <article class="post-card">
                    <?php if($p['featured_image']): ?>
                        <img src="<?php echo SITE_URL . $p['featured_image']; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="post-image">
                    <?php else: ?>
                        <div class="post-image"></div>
                    <?php endif; ?>
                    <div class="post-content">
                        <h2 class="post-title">
                            <a href="/post/<?php echo $p['slug']; ?>"><?php echo sanitize_text($p['title']); ?></a>
                        </h2>
                        <div class="post-meta">
                            Bởi <?php echo sanitize_text($p['author_name']); ?> • <?php echo format_date($p['created_at']); ?>
                        </div>
                        <div class="post-excerpt">
                            <?php echo sanitize_text(substr($p['excerpt'] ?: $p['content'], 0, 150)); ?>...
                        </div>
                        <a href="/post/<?php echo $p['slug']; ?>" class="read-more">Đọc thêm →</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Powered by SimpleCMS.</p>
    </footer>
</body>
</html>

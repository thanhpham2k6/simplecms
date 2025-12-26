<?php
require_once 'config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/post.class.php';

$post = new Post();

if(!isset($_GET['slug'])) {
    redirect('/');
}

$post_data = $post->getBySlug($_GET['slug']);

if(!$post_data) {
    header("HTTP/1.0 404 Not Found");
    echo "Bài viết không tồn tại.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize_text($post_data['title']); ?> - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo sanitize_text(substr($post_data['excerpt'] ?: $post_data['content'], 0, 160)); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
        
        header { background: #fff; border-bottom: 1px solid #e1e4e8; }
        .header-container { max-width: 1200px; margin: 0 auto; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        .site-title { font-size: 28px; font-weight: 700; color: #2271b1; text-decoration: none; }
        nav ul { display: flex; list-style: none; gap: 20px; }
        nav a { color: #333; text-decoration: none; font-weight: 500; }
        nav a:hover { color: #2271b1; }
        
        .container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        
        .post-header { margin-bottom: 30px; }
        .post-title { font-size: 42px; font-weight: 700; color: #1d2327; margin-bottom: 15px; line-height: 1.2; }
        .post-meta { color: #646970; font-size: 16px; margin-bottom: 20px; }
        
        .featured-image { width: 100%; height: auto; border-radius: 8px; margin-bottom: 30px; }
        
        .post-content { font-size: 18px; line-height: 1.8; color: #1d2327; }
        .post-content p { margin-bottom: 20px; }
        .post-content h2 { font-size: 32px; margin: 30px 0 15px; }
        .post-content h3 { font-size: 24px; margin: 25px 0 12px; }
        .post-content ul, .post-content ol { margin: 20px 0 20px 30px; }
        .post-content li { margin-bottom: 8px; }
        .post-content blockquote { border-left: 4px solid #2271b1; padding-left: 20px; margin: 20px 0; font-style: italic; color: #646970; }
        .post-content code { background: #f6f8fa; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .post-content pre { background: #f6f8fa; padding: 15px; border-radius: 5px; overflow-x: auto; margin: 20px 0; }
        
        .back-link { margin-top: 40px; padding-top: 30px; border-top: 1px solid #e1e4e8; }
        .back-link a { color: #2271b1; text-decoration: none; font-weight: 600; }
        .back-link a:hover { text-decoration: underline; }
        
        footer { background: #1d2327; color: #fff; padding: 40px 20px; margin-top: 60px; text-align: center; }
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
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <article>
            <div class="post-header">
                <h1 class="post-title"><?php echo sanitize_text($post_data['title']); ?></h1>
                <div class="post-meta">
                    Bởi <?php echo sanitize_text($post_data['author_name']); ?> • 
                    <?php echo format_date($post_data['created_at']); ?>
                </div>
            </div>
            
            <?php if($post_data['featured_image']): ?>
                <img src="<?php echo SITE_URL . $post_data['featured_image']; ?>" 
                     alt="<?php echo htmlspecialchars($post_data['title']); ?>" 
                     class="featured-image">
            <?php endif; ?>
            
            <div class="post-content">
                <?php echo nl2br(sanitize_text($post_data['content'])); ?>
            </div>
            
            <div class="back-link">
                <a href="/">← Quay lại trang chủ</a>
            </div>
        </article>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Powered by SimpleCMS.</p>
    </footer>
</body>
</html>

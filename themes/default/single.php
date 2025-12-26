<?php
// Template for single post
require_once '../../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/post.class.php';

$post = new Post();

if(!isset($_GET['slug'])) {
    header("Location: " . SITE_URL);
    exit;
}

$post_data = $post->getBySlug($_GET['slug']);

if(!$post_data) {
    header("HTTP/1.0 404 Not Found");
    echo "Post not found";
    exit;
}

$page_title = $post_data['title'];
include 'header.php';
?>

<article class="single-post">
    <h1 class="post-title"><?php echo sanitize_text($post_data['title']); ?></h1>
    
    <div class="post-meta">
        <span class="author">By <?php echo sanitize_text($post_data['author_name']); ?></span>
        <span class="date"><?php echo format_date($post_data['created_at']); ?></span>
    </div>
    
    <?php if($post_data['featured_image']): ?>
        <div class="post-featured-image">
            <img src="<?php echo SITE_URL . $post_data['featured_image']; ?>" alt="<?php echo htmlspecialchars($post_data['title']); ?>">
        </div>
    <?php endif; ?>
    
    <div class="post-content">
        <?php echo nl2br(sanitize_text($post_data['content'])); ?>
    </div>
</article>

<?php include 'footer.php'; ?>

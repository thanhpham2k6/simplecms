<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo SITE_NAME; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f0f0f1; }
        .admin-container { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: #1d2327; color: #fff; padding: 0; }
        .sidebar-header { padding: 20px; background: #2c3338; border-bottom: 1px solid #3c434a; }
        .sidebar-header h2 { font-size: 20px; }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { border-bottom: 1px solid #3c434a; }
        .sidebar-menu a { display: block; padding: 12px 20px; color: #fff; text-decoration: none; transition: all 0.2s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #2271b1; }
        
        /* Main content */
        .main-content { flex: 1; padding: 30px; }
        .top-bar { background: white; padding: 15px 30px; margin: -30px -30px 30px -30px; box-shadow: 0 1px 1px rgba(0,0,0,0.04); display: flex; justify-content: space-between; align-items: center; }
        .user-info { display: flex; align-items: center; gap: 10px; }
        
        /* Dashboard */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card h3 { font-size: 14px; color: #646970; margin-bottom: 10px; }
        .stat-number { font-size: 36px; font-weight: 600; color: #2271b1; }
        
        /* Tables */
        .wp-table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .wp-table th { background: #f6f7f7; padding: 12px 15px; text-align: left; font-weight: 600; color: #2c3338; }
        .wp-table td { padding: 12px 15px; border-top: 1px solid #dcdcde; }
        .wp-table tr:hover { background: #f6f7f7; }
        
        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #1d2327; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #dcdcde; border-radius: 4px; font-size: 14px; }
        .form-group textarea { min-height: 200px; font-family: inherit; }
        
        /* Buttons */
        .btn { display: inline-block; padding: 10px 20px; background: #2271b1; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }
        .btn:hover { background: #135e96; }
        .btn-danger { background: #d63638; }
        .btn-danger:hover { background: #b32d2e; }
        .btn-secondary { background: #6c757d; }
        
        /* Status badges */
        .status-published { color: #00a32a; font-weight: 600; }
        .status-draft { color: #996800; font-weight: 600; }
        .status-private { color: #646970; font-weight: 600; }
        
        h1 { margin-bottom: 20px; color: #1d2327; }
        h2 { margin-bottom: 15px; color: #1d2327; }
        .recent-posts { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo SITE_NAME; ?></h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="posts.php">Bài viết</a></li>
                <li><a href="new-post.php">Thêm bài viết</a></li>
                <li><a href="categories.php">Danh mục</a></li>
                <li><a href="users.php">Người dùng</a></li>
                <li><a href="themes.php">Giao diện</a></li>
                <li><a href="plugins.php">Plugin</a></li>
                <li><a href="settings.php">Cài đặt</a></li>
                <li><a href="../index.php" target="_blank">Xem trang web</a></li>
                <li><a href="logout.php">Đăng xuất</a></li>
            </ul>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <div>SimpleCMS Admin Panel</div>
                <div class="user-info">
                    <span>Xin chào, <strong><?php echo sanitize_text($_SESSION['username']); ?></strong></span>
                </div>
            </div>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/themes/default/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">
                <a href="<?php echo SITE_URL; ?>"><?php echo SITE_NAME; ?></a>
            </h1>
            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>">Trang chủ</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/admin">Quản trị</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main class="site-content">
        <div class="container">

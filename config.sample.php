<?php
/**
 * SimpleCMS Configuration
 * Copy this file to config.php and update values
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'simplecms');
define('DB_USER', 'simplecms_user');
define('DB_PASS', 'your_password_here');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'SimpleCMS');
define('SITE_DESC', 'Open Source Content Management System');

// Path Configuration
define('ROOT_PATH', dirname(__FILE__));
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('THEMES_PATH', ROOT_PATH . '/themes');
define('PLUGINS_PATH', ROOT_PATH . '/plugins');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// Security Keys - Generate new ones at https://api.wordpress.org/secret-key/1.1/salt/
define('AUTH_KEY', 'put-your-unique-phrase-here');
define('SECURE_AUTH_KEY', 'put-your-unique-phrase-here');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Debug Mode (set to false in production)
define('DEBUG_MODE', false);

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Error Reporting
if(DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

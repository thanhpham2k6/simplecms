<?php
/**
 * SecurityNew Class - Xử lý các vấn đề bảo mật cho SimpleCMS
 * 
 * File: includes/SecurityNew.php
 * 
 * KHÔNG ảnh hưởng đến file Security.php cũ
 * Sử dụng độc lập cho các tính năng bảo mật mới
 * 
 * @version 1.0.0
 * @date 2025-01-15
 */

class SecurityNew {
    
    /**
     * Generate CSRF Token
     * @return string
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validate CSRF Token
     * @param string $token
     * @return bool
     */
    public static function validateCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get CSRF Token Input Field
     * @return string HTML input field
     */
    public static function getCSRFInput() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Sanitize input data
     * @param mixed $data
     * @return mixed
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        
        return $data;
    }
    
    /**
     * Rate Limiting - Chống brute force login
     * @param string $identifier (IP hoặc username)
     * @param int $max_attempts
     * @param int $time_window (seconds)
     * @return array ['allowed' => bool, 'remaining' => int, 'reset_time' => int]
     */
    public static function checkRateLimit($identifier, $max_attempts = 5, $time_window = 900) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_' . md5($identifier);
        $current_time = time();
        
        // Khởi tạo nếu chưa có
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 0,
                'first_attempt' => $current_time,
                'last_attempt' => $current_time
            ];
        }
        
        $rate_data = $_SESSION[$key];
        
        // Reset nếu đã hết thời gian chặn
        if ($current_time - $rate_data['first_attempt'] > $time_window) {
            $_SESSION[$key] = [
                'attempts' => 0,
                'first_attempt' => $current_time,
                'last_attempt' => $current_time
            ];
            $rate_data = $_SESSION[$key];
        }
        
        $remaining = $max_attempts - $rate_data['attempts'];
        $reset_time = $rate_data['first_attempt'] + $time_window;
        
        return [
            'allowed' => $rate_data['attempts'] < $max_attempts,
            'remaining' => max(0, $remaining),
            'reset_time' => $reset_time,
            'wait_seconds' => max(0, $reset_time - $current_time)
        ];
    }
    
    /**
     * Record failed login attempt
     * @param string $identifier
     */
    public static function recordFailedAttempt($identifier) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 0,
                'first_attempt' => time(),
                'last_attempt' => time()
            ];
        }
        
        $_SESSION[$key]['attempts']++;
        $_SESSION[$key]['last_attempt'] = time();
    }
    
    /**
     * Reset rate limit (sau khi login thành công)
     * @param string $identifier
     */
    public static function resetRateLimit($identifier) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_' . md5($identifier);
        unset($_SESSION[$key]);
    }
    
    /**
     * Validate password strength
     * @param string $password
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password phải có ít nhất 8 ký tự';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password phải có ít nhất 1 chữ hoa';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password phải có ít nhất 1 chữ thường';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password phải có ít nhất 1 số';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password phải có ít nhất 1 ký tự đặc biệt';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Hash password securely
     * @param string $password
     * @return string
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
    }
    
    /**
     * Verify password
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Prevent SQL Injection - Prepare statement helper
     * @param mysqli $conn
     * @param string $query
     * @param array $params
     * @param string $types
     * @return mysqli_result|bool
     */
    public static function executeQuery($conn, $query, $params = [], $types = '') {
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            error_log("MySQL prepare error: " . $conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $result = $stmt->execute();
        
        if ($result === false) {
            error_log("MySQL execute error: " . $stmt->error);
            $stmt->close();
            return false;
        }
        
        $return = $stmt->get_result();
        $stmt->close();
        
        return $return;
    }
    
    /**
     * Validate email
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate URL
     * @param string $url
     * @return bool
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Generate secure random token
     * @param int $length
     * @return string
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Check if request is AJAX
     * @return bool
     */
    public static function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get client IP address
     * @return string
     */
    public static function getClientIP() {
        $ip_keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Secure session configuration
     */
    public static function configureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
            ini_set('session.cookie_samesite', 'Strict');
            
            session_name('SIMPLECMS_SESSION');
            session_start();
            
            // Regenerate session ID để chống session fixation
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id(true);
                $_SESSION['initiated'] = true;
            }
        }
    }
    
    /**
     * Log security events
     * @param string $event
     * @param array $data
     */
    public static function logSecurityEvent($event, $data = []) {
        $log_file = __DIR__ . '/../logs/security.log';
        $log_dir = dirname($log_file);
        
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => self::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'data' => $data
        ];
        
        file_put_contents(
            $log_file,
            json_encode($log_entry) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
?>
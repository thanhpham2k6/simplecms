<?php
function generate_csrf_token() {
    if(empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function xss_clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function check_rate_limit($identifier, $max_attempts = 5, $time_window = 300) {
    $cache_file = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    
    if(file_exists($cache_file)) {
        $data = json_decode(file_get_contents($cache_file), true);
        if(time() - $data['time'] < $time_window) {
            if($data['attempts'] >= $max_attempts) {
                return false;
            }
            $data['attempts']++;
        } else {
            $data = ['time' => time(), 'attempts' => 1];
        }
    } else {
        $data = ['time' => time(), 'attempts' => 1];
    }
    
    file_put_contents($cache_file, json_encode($data));
    return true;
}

function login_attempt_check($username) {
    return check_rate_limit('login_' . $username, 5, 900); // 5 lần trong 15 phút
}

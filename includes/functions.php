<?php
function sanitize_text($text) {
    return htmlspecialchars(strip_tags($text), ENT_QUOTES, 'UTF-8');
}

function sanitize_email($email) {
    return filter_var($email, FILTER_SANITIZE_EMAIL);
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function get_current_user() {
    if(!is_logged_in()) return null;
    $user = new User();
    return $user->getById($_SESSION['user_id']);
}

function format_date($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function create_slug($text) {
    $text = preg_replace('/[^\p{L}\p{N}\s-]/u', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return mb_strtolower($text, 'UTF-8');
}

function upload_file($file, $allowed_types = ['image/jpeg', 'image/png', 'image/gif']) {
    if(!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if(!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $upload_path = UPLOADS_PATH . '/' . date('Y/m');
    
    if(!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    $destination = $upload_path . '/' . $filename;
    
    if(move_uploaded_file($file['tmp_name'], $destination)) {
        return str_replace(ROOT_PATH, '', $destination);
    }
    
    return false;
}

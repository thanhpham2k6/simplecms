<?php
/*
Plugin Name: Sample Plugin
Description: Đây là một plugin mẫu cho SimpleCMS
Version: 1.0.0
Author: SimpleCMS Team
*/

// Activation hook
function sample_plugin_activate() {
    // Code chạy khi plugin được activate
    error_log('Sample Plugin đã được kích hoạt!');
}

// Deactivation hook
function sample_plugin_deactivate() {
    // Code chạy khi plugin được deactivate
    error_log('Sample Plugin đã bị tắt!');
}

// Thêm chức năng vào CMS
function sample_plugin_init() {
    // Code của plugin chạy mỗi khi trang load
    // Ví dụ: thêm menu, hook vào hệ thống, etc.
}

// Hook vào hệ thống
add_action('init', 'sample_plugin_init');

// Hàm helper để add action/filter
function add_action($hook, $callback) {
    global $actions;
    if(!isset($actions[$hook])) {
        $actions[$hook] = [];
    }
    $actions[$hook][] = $callback;
}

function do_action($hook) {
    global $actions;
    if(isset($actions[$hook])) {
        foreach($actions[$hook] as $callback) {
            call_user_func($callback);
        }
    }
}

// Example: Thêm custom function
function sample_plugin_custom_function() {
    return "Hello from Sample Plugin!";
}
?>

<?php
/**
 * Plugin Name: AI Chat
 * Plugin URI: https://yourwebsite.com/
 * Description: Chat with AI directly from your WordPress site.
 * Version: 1.0
 * Author: Hoan Truong
 * License: GPL2
 */


require_once plugin_dir_path(__FILE__) . 'includes/utils.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-api-chat-ai.php';
require_once plugin_dir_path(__FILE__) . 'includes/db-functions.php';


add_action('wp_footer', 'ai_chat_render_html');
function ai_chat_render_html()
{

    $include_path = plugin_dir_path(__FILE__);

    // Load button template
    if (file_exists($include_path . 'templates/button.php')) {
        include $include_path . 'templates/button.php';
    }

    // Load chat box template
    if (file_exists($include_path . 'templates/box-chat.php')) {
        include $include_path . 'templates/box-chat.php';
    }

}

// Nhúng file CSS và JS
add_action('wp_enqueue_scripts', 'ai_chat_enqueue_assets');
function ai_chat_enqueue_assets()
{
    wp_enqueue_style('ai-chat-vars', plugins_url('assets/css/variables.css', __FILE__));
    wp_enqueue_style('ai-chat-style', plugins_url('assets/css/style.css', __FILE__), array('ai-chat-vars'));
    wp_enqueue_style('ai-chat-chat-box-style', plugins_url('assets/css/chat-box.css', __FILE__), array('ai-chat-vars'));
    wp_enqueue_script('fingerprintjs', plugins_url('assets/scripts/fingerprint.min.js', __FILE__), array(), '5.0.1', true);

    wp_enqueue_script('init-fingerprint-script', plugins_url('assets/scripts/figerprint-init.js', __FILE__), array('jquery', 'fingerprintjs'), '1.0', true);
    wp_enqueue_script('ai-chat-script', plugins_url('assets/scripts/script.js', __FILE__), array('jquery', 'init-fingerprint-script'), '1.0', true);
    wp_enqueue_script('ai-chat-chat-box-script', plugins_url('assets/scripts/chat-box.js', __FILE__), array('jquery', 'init-fingerprint-script'), '1.0', true);

    // Truyền biến vào Javascript
    wp_localize_script('ai-chat-script', 'aiChatSettings', array(
        'root' => esc_url_raw(rest_url()), // URL gốc của REST API
        'nonce' => wp_create_nonce('wp_rest') // Mã bảo mật để WP cho phép gọi API
    ));
}


register_activation_hook(__FILE__, 'ai_chat_create_table');
function ai_chat_create_table()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // 1. Bảng Session (Cuộc hội thoại)
    $table_sessions = $wpdb->prefix . 'ai_chat_sessions';
    $sql_sessions = "CREATE TABLE $table_sessions (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        title varchar(255) DEFAULT 'Cuộc trò chuyện mới',
        user_id bigint(20) DEFAULT NULL,
        visitor_fingerprint varchar(255) DEFAULT NULL,
        is_active tinyint(1) DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // 2. Bảng Messages (Chi tiết tin nhắn)
    $table_messages = $wpdb->prefix . 'ai_chat_messages';
    $sql_messages = "CREATE TABLE $table_messages (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        session_id bigint(20) NOT NULL, -- Khóa ngoại liên kết với bảng Session
        role varchar(20) NOT NULL,      -- 'user' hoặc 'assistant'
        content text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY session_id (session_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_sessions);
    dbDelta($sql_messages);
}

add_action('rest_api_init', ['API_Chat_AI', 'register_routes']);

add_action('admin_menu', 'ai_chat_plugin_admin_menu');

function ai_chat_plugin_admin_menu()
{
    // 1. Tạo Menu Chính (Parent)
    add_menu_page(
        'AI Chat Dashboard',         // Tiêu đề trang
        'AI Chat AI',                // Tên menu hiển thị
        'manage_options',            // Quyền hạn
        'ai-chat-main',              // Menu Slug (ID của menu chính)
        'ai_chat_render_main_page',  // Hàm hiển thị trang tổng quan
        'dashicons-format-chat',     // Icon
        25
    );

    // Sửa tên menu con đầu tiên (trùng slug với menu cha)
    add_submenu_page(
        'ai-chat-main',
        'Danh sách hội thoại',
        'Lịch sử Chat',
        'manage_options',
        'ai-chat-main', // Trùng slug với cha
        'ai_chat_render_main_page'
    );


    // 3. Tạo Submenu: Cấu hình (Settings)
    add_submenu_page(
        'ai-chat-main',
        'Cấu hình AI',
        'Cài đặt',
        'manage_options',
        'ai-chat-settings',
        'ai_chat_render_settings_page'
    );
}
<?php
/**
 * Plugin Name: AI Chat
 * Plugin URI: https://yourwebsite.com/
 * Description: Chat with AI directly from your WordPress site.
 * Version: 1.0
 * Author: Hoan Truong
 * License: GPL2
 */


require_once plugin_dir_path(__FILE__) . 'includes/class-api-chat-ai.php';


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
    wp_enqueue_script('ai-chat-script', plugins_url('assets/scripts/script.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('ai-chat-chat-box-script', plugins_url('assets/scripts/chat-box.js', __FILE__), array('jquery'), '1.0', true);

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
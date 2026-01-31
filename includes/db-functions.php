<?php

/**
 * Lưu tin nhắn vào Database
 */
function save_ai_chat_message($session_id, $role, $content)
{
    global $wpdb;
    return $wpdb->insert(
        $wpdb->prefix . 'ai_chat_messages',
        array(
            'session_id' => $session_id,
            'role' => $role,
            'content' => $content,
            'created_at' => current_time('mysql')
        )
    );
}

/**
 * Tạo phiên chat mới
 */
function create_ai_chat_session($user_id = null, $title = 'Cuộc trò chuyện mới')
{
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'ai_chat_sessions',
        array(
            'title' => $title,
            'user_id' => $user_id,
            'created_at' => current_time('mysql')
        )
    );
    return $wpdb->insert_id; // Trả về ID vừa tạo để dùng cho các tin nhắn sau
}
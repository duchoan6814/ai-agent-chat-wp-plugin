<?php

/**
 * Lưu tin nhắn vào Database
 * @param mixed $session_id
 * @param mixed $role
 * @param mixed $content
 * @return int|false Số dòng bị ảnh hưởng hoặc false nếu lỗi
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
 * Tạo mới một phiên trò chuyện và trả về ID của nó 
 * @param mixed $visitor_id
 * @param mixed $title
 * @return int
 */
function create_ai_chat_session($visitor_id, $title = 'Cuộc trò chuyện mới')
{
    global $wpdb;
    $wpdb->insert(
        $wpdb->prefix . 'ai_chat_sessions',
        array(
            'visitor_fingerprint' => $visitor_id,
            'is_active' => 1,
            'title' => $title,
            'created_at' => current_time('mysql')
        )
    );
    return $wpdb->insert_id; // Trả về ID vừa tạo để dùng cho các tin nhắn sau
}
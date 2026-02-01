<?php


/**
 * Chuyển đổi tin nhắn của người dùng thành title cho phiên chat, dài quá thì căt bớt và thêm "..." sao cho ít hơm 255 ký tự
 * @param mixed $message
 * @return string
 */
function sanitize_message($message)
{
    // Loại bỏ các thẻ HTML và ký tự không mong muốn
    $clean_message = wp_strip_all_tags($message);
    $clean_message = trim($clean_message);
    if (strlen($clean_message) > 255) {
        $clean_message = substr($clean_message, 0, 252) . '...';
    }
    return $clean_message;
}
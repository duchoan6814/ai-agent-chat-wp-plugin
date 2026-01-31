<?php
/**
 * Plugin Name: AI Chat
 * Plugin URI: https://yourwebsite.com/
 * Description: Chat with AI directly from your WordPress site.
 * Version: 1.0
 * Author: Hoan Truong
 * License: GPL2
 */




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
    wp_enqueue_style('ai-chat-style', plugins_url('assets/css/style.css', __FILE__));
    wp_enqueue_style('ai-chat-chat-box-style', plugins_url('assets/css/chat-box.css', __FILE__));
    wp_enqueue_script('ai-chat-script', plugins_url('assets/scripts/script.js', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('ai-chat-chat-box-script', plugins_url('assets/scripts/chat-box.js', __FILE__), array('jquery'), '1.0', true);
}
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
    ?>
    <div id="ai-chat-button">
        <img src="https://cdn-icons-png.flaticon.com/512/8943/8943377.png" alt="Chat">
    </div>
    <?php
}

// Nhúng file CSS và JS
add_action('wp_enqueue_scripts', 'ai_chat_enqueue_assets');
function ai_chat_enqueue_assets()
{
    wp_enqueue_style('ai-chat-style', plugins_url('assets/style.css', __FILE__));
    wp_enqueue_script('ai-chat-script', plugins_url('assets/script.js', __FILE__), array('jquery'), '1.0', true);
}
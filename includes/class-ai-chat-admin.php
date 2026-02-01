<?php

class AIChat_Admin
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_menus'));
    }

    public function register_menus()
    {
        // 1. Tạo Menu Chính (Parent)
        add_menu_page(
            'Danh sách hội thoại',         // Tiêu đề trang
            'AI Chat AI',                // Tên menu hiển thị
            'manage_options',            // Quyền hạn
            'ai-chat-main',              // Menu Slug (ID của menu chính)
            array($this, 'ai_chat_render_main_page'),  // Hàm hiển thị trang tổng quan
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
            array($this, 'ai_chat_render_main_page')
        );


        // 3. Tạo Submenu: Cấu hình (Settings)
        add_submenu_page(
            'ai-chat-main',
            'Cấu hình AI',
            'Cài đặt',
            'manage_options',
            'ai-chat-settings',
            array($this, 'ai_chat_render_settings_page')
        );
    }


    public function ai_chat_render_main_page()
    {
        require_once plugin_dir_path(__FILE__) . 'class-ai-chat-session-table.php';

        $myListTable = new AI_Chat_Session_Table();
        $myListTable->prepare_items();


        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Lịch sử Chat</h1>
            <form method="post">
                <?php
                $myListTable->display(); // Hiển thị bảng
                ?>
            </form>
        </div>
        <?php

    }



    public function ai_chat_render_settings_page()
    {
        echo '<div class="wrap"><h1>Cài đặt AI Chat</h1></div>';
    }
}
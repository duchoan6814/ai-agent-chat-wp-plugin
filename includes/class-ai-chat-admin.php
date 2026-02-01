<?php

class AIChat_Admin
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'register_menus'));
        add_action('admin_init', [$this, 'register_settings_logic']);
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

        // Sửa tên menu con đầu tiên (trùng slug với menu cha)
        add_submenu_page(
            null,
            'Chi tiết hội thoại',
            'Chi tiết hội thoại',
            'manage_options',
            'ai-chat-details', // Trùng slug với cha
            array($this, 'ai_chat_render_detail_page')
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

    // Đăng ký các field với WordPress
    public function register_settings_logic()
    {
        register_setting('ai_chat_settings_group', 'ai_chat_settings');

        add_settings_section(
            'ai_chat_main_section',
            'Kết nối FastAPI Service',
            null,
            'ai-chat-settings-page'
        );

        add_settings_field(
            'ai_service_url',
            'AI Service URL',
            [$this, 'url_field_callback'],
            'ai-chat-settings-page',
            'ai_chat_main_section'
        );

        add_settings_field(
            'permanent_access_token',
            'Permanent Access Token',
            [$this, 'token_field_callback'],
            'ai-chat-settings-page',
            'ai_chat_main_section'
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

    public function ai_chat_render_detail_page()
    {
        require_once plugin_dir_path(__FILE__) . 'class-ai-chat-message-table.php';

        $myListTable = new AI_Chat_Message_Table();
        $myListTable->prepare_items();


        ?>
        <div class="wrap">
            <?php
            // Lấy params từ URL để quay lại trang trước với đúng filter
            $back_url = admin_url('admin.php?page=ai-chat-main');
            if (isset($_GET['back_params'])) {
                $back_params = urldecode($_GET['back_params']);
                $back_url = admin_url('admin.php?page=ai-chat-main&' . $back_params);
            }
            ?>
            <a href="<?php echo esc_url($back_url); ?>" class="page-title-action">&larr; Quay lại</a>
            <h1 class="wp-heading-inline">Chi tiết hội thoại</h1>

            <form method="post">
                <?php
                $myListTable->display(); // Hiển thị bảng
                ?>
            </form>
        </div>
        <?php
    }

    // Callbacks hiển thị ô nhập liệu
    public function url_field_callback()
    {
        $options = get_option('ai_chat_settings');
        $value = $options['ai_service_url'] ?? '';
        echo '<input type="text" name="ai_chat_settings[ai_service_url]" value="' . esc_attr($value) . '" class="regular-text" placeholder="https://api.yourdomain.com">';
        echo '<p class="description">Đường dẫn API trỏ tới dịch vụ FastAPI của bạn.</p>';
    }

    public function token_field_callback()
    {
        $options = get_option('ai_chat_settings');
        $value = $options['permanent_access_token'] ?? '';
        echo '<input type="password" name="ai_chat_settings[permanent_access_token]" value="' . esc_attr($value) . '" class="regular-text">';
        echo '<p class="description">Token dùng để xác thực với FastAPI (X-API-Key hoặc Bearer).</p>';
    }

    public function ai_chat_render_settings_page()
    {

        include plugin_dir_path(__FILE__) . '../templates/admin-ai-chat-settings.php';
    }
}
<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>Cấu hình AI Chat Service</h1>
    
    <?php settings_errors(); // Hiển thị thông báo lưu thành công/thất bại ?>

    <form method="post" action="options.php">
        <?php
            // Đăng ký nhóm cài đặt (phải trùng với tên trong class Controller)
            settings_fields(option_group: 'ai_chat_settings_group');
            // Hiển thị các section
            do_settings_sections('ai-chat-settings-page');
            // Nút lưu
            submit_button('Lưu cấu hình');
        ?>
    </form>
</div>
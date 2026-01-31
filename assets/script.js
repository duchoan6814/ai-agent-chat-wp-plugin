jQuery(document).ready(function($) {
    // Bây giờ bạn có thể dùng dấu $ thoải mái bên trong khối này
    
    const $windowChat = $('#ai-chat-window');
    const $chatContent = $('#chat-content');

    // 1. Click vào nút tròn để hiện khung chat
    $('#ai-chat-button').on('click', function() {
        $windowChat.fadeToggle(300); // Hiệu ứng hiện ra mượt mà
    });

    // 2. Click nút đóng
    $('#close-chat').on('click', function() {
        $windowChat.fadeOut(300);
    });

    // 3. Xử lý gửi tin nhắn
    $('#send-btn').on('click', function() {
        let message = $('#user-msg').val();
        
        if (message.trim() !== "") {
            // Hiển thị tin nhắn người dùng
            $chatContent.append(`<div class="user-msg"><b>Bạn:</b> ${message}</div>`);
            $('#user-msg').val(''); // Xóa ô input

            // Cuộn xuống đáy khung chat
            $chatContent.scrollTop($chatContent[0].scrollHeight);

            // Giả lập AI trả lời (Sau này sẽ thay bằng gọi API thật)
            setTimeout(function() {
                $chatContent.append(`<div class="ai-msg"><b>AI:</b> Đang suy nghĩ...</div>`);
            }, 500);
        }
    });
});
jQuery(document).ready(function ($) {
  const $windowChat = $("#ai-chat-window");
  const $chatContent = $("#chat-content");

  // 3. Xử lý gửi tin nhắn
  $("#send-btn").on("click", function () {
    let message = $("#user-msg").val();

    if (message.trim() !== "") {
      // Hiển thị tin nhắn người dùng
      $chatContent.append(`<div class="message user-msg">
            <div class="msg-bubble">${message}</div>
        </div>`);
      $("#user-msg").val(""); // Xóa ô input

      // Cuộn xuống đáy khung chat
      $chatContent.scrollTop($chatContent[0].scrollHeight);

      // Giả lập AI trả lời (Sau này sẽ thay bằng gọi API thật)
      setTimeout(function () {
        $chatContent.append(
          `<div class="message ai-msg">
            <div class="msg-bubble">Chào bạn! Tôi có thể giúp gì cho bạn hôm nay?</div>
        </div>`,
        );
      }, 500);
    }
  });

  // 2. Click nút đóng
  $("#close-chat").on("click", function () {
    $windowChat.fadeOut(300);
  });
});

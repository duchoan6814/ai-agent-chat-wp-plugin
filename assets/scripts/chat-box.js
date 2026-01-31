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

      $.ajax({
        url: aiChatSettings.root + "ai-chat/v1/send-message",
        method: "POST",
        beforeSend: function (xhr) {
          xhr.setRequestHeader("X-WP-Nonce", aiChatSettings.nonce);
        },
        data: {
          message: message,
          session_id: "1",
        },
        success: function (response) {
          if (response.status === "success") {
            $chatContent.append(
              `<div class="message ai-msg">
                  <div class="msg-bubble">${response?.data}</div>
              </div>`,
            );
          }
        },
        error: function (error) {
          $chatContent.append(
            `<div class="message ai-msg">
                  <div class="msg-bubble msg-error">${error.responseJSON.message || "Đã có lỗi xảy ra"}</div>
              </div>`,
          );
        },

        complete: function () {
          // Cuộn xuống đáy khung chat
          $chatContent.scrollTop($chatContent[0].scrollHeight);
        },
      });
    }
  });

  // 2. Click nút đóng
  $("#close-chat").on("click", function () {
    $windowChat.fadeOut(300);
  });
});

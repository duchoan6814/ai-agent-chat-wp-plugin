jQuery(document).ready(function ($) {
  // Bây giờ bạn có thể dùng dấu $ thoải mái bên trong khối này

  const $windowChat = $("#ai-chat-window");

  // 1. Click vào nút tròn để hiện khung chat
  $("#ai-chat-button").on("click", function () {
    $windowChat.fadeToggle(300); // Hiệu ứng hiện ra mượt mà

    if ($windowChat.is(":visible")) {
      // TODO: fetch current session if needed

      // Nếu đã có sessionId rồi thì không cần gọi API nữa
      if (window.AIChatPlugin.currentSessionId > 0) {
        console.log(
          "Đã có session đang hoạt động:",
          window.AIChatPlugin.currentSessionId,
        );
        return;
      }

      // Nếu chưa có, tiến hành lấy session từ server
      fetchActiveSession();
    }
  });

  function fetchActiveSession() {
    // Đảm bảo visitorId đã sẵn sàng (từ FingerprintJS)
    const visitorId = window.AIChatPlugin.visitorId;

    if (!visitorId) {
      console.warn("Chưa có Device ID, đang đợi...");
      return;
    }

    $.ajax({
      url: aiChatSettings.root + "ai-chat/v1/get-current-session",
      method: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("X-WP-Nonce", aiChatSettings.nonce);
        xhr.setRequestHeader("X-Visitor-ID", visitorId);
      },
      success: function (response) {
        if (response.data > 0) {
          window.AIChatPlugin.currentSessionId = response.data;
          console.log("Khôi phục Session:", response.data);

          // Gọi hàm load tin nhắn cũ đổ vào giao diện (nếu muốn)
          // loadChatHistory(response.data);
        } else {
          console.log(
            "Không có session cũ, sẽ tạo mới khi gửi tin nhắn đầu tiên.",
          );
        }
      },
    });
  }
});

jQuery(document).ready(function ($) {
  const $windowChat = $("#ai-chat-window");
  const $chatContent = $("#chat-content");

  // 3. Xử lý gửi tin nhắn
  $("#send-btn").on("click", async function () {
    let message = $("#user-msg").val();

    if (message.trim() !== "") {
      // Hiển thị tin nhắn người dùng
      $chatContent.append(`<div class="message user-msg">
            <div class="msg-bubble">${message}</div>
        </div>`);
      $("#user-msg").val(""); // Xóa ô input

      // Cuộn xuống đáy khung chat
      $chatContent.scrollTop($chatContent[0].scrollHeight);

      try {
        const response = await fetch(
          aiChatSettings.root + "ai-chat/v1/send-message",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-WP-Nonce": aiChatSettings.nonce,
              "X-Visitor-Id": window.AIChatPlugin.visitorId,
            },
            body: JSON.stringify({
              message: message,
              session_id: window.AIChatPlugin.currentSessionId || 0,
            }),
          },
        );

        if (!response.ok) {
          throw new Error("Không thể gửi tin nhắn!");
        }

        const sessionIdFromServer = response.headers.get("X-Chat-Session-Id");
        // Cập nhật sessionId nếu server trả về
        if (sessionIdFromServer && !window.AIChatPlugin.currentSessionId) {
          window.AIChatPlugin.currentSessionId = parseInt(sessionIdFromServer);
          console.log(
            "Đã thiết lập Session ID từ server:",
            window.AIChatPlugin.currentSessionId,
          );
        }
        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        const $assistantTyping = $(`<div class="message ai-msg">
                  <div class="msg-bubble"></div>
              </div>`);

        // Tạo một khung tin nhắn trống cho AI trước
        let aiMsgDiv = $chatContent.append($assistantTyping);

        while (true) {
          const { done, value } = await reader.read();
          if (done) break;

          const chunk = decoder.decode(value, { stream: true });

          // Cập nhật nội dung tin nhắn dần dần (Giả sử FastAPI trả về text thuần)
          $assistantTyping.find(".msg-bubble").append(chunk);

          $chatContent.scrollTop($chatContent[0].scrollHeight);
        }
      } catch (error) {
        $chatContent.append(
          `<div class="message ai-msg">
                  <div class="msg-bubble msg-error">${error?.message || "Đã có lỗi xảy ra"}</div>
              </div>`,
        );
      }
    }
  });

  // 2. Click nút đóng
  $("#close-chat").on("click", function () {
    $windowChat.fadeOut(300);
  });
});

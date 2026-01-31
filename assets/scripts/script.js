jQuery(document).ready(function ($) {
  // Bây giờ bạn có thể dùng dấu $ thoải mái bên trong khối này

  const $windowChat = $("#ai-chat-window");

  // 1. Click vào nút tròn để hiện khung chat
  $("#ai-chat-button").on("click", function () {
    $windowChat.fadeToggle(300); // Hiệu ứng hiện ra mượt mà
  });
});

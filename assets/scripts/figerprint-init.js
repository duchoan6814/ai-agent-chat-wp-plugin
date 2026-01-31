// Tạo một object global duy nhất cho plugin của bạn nếu chưa có
window.AIChatPlugin = window.AIChatPlugin || {};

// Initialize the agent at application startup.
var fpPromise = FingerprintJS.load();

// Analyze the visitor when necessary.
fpPromise
  .then((fp) => fp.get())
  .then((result) => {
    // Lưu vào biến global
    window.AIChatPlugin.visitorId = result.visitorId;

    // Thông báo cho các thành phần khác rằng ID đã sẵn sàng
    document.dispatchEvent(
      new CustomEvent("ai_chat_ready", {
        detail: { visitorId: result.visitorId },
      }),
    );

    console.log("Global ID initialized:", window.AIChatPlugin.visitorId);
  });

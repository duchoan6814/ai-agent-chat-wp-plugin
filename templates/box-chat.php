<div id="ai-chat-window" class="ai-chat-container" style="display: none;">
    <div class="chat-header">
        <div class="header-info">
            <span class="status-dot"></span>
            <span class="chat-title">AI Assistant</span>
        </div>
        <button id="close-chat" class="close-btn">&times;</button>
    </div>

    <div id="chat-content" class="chat-body">
        <div class="message ai-msg">
            <div class="msg-bubble">Chào bạn! Tôi có thể giúp gì cho bạn hôm nay?</div>
        </div>
        </div>

    <div class="chat-footer">
        <div class="input-wrapper">
            <label for="file-upload" class="attach-btn">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg>
            </label>
            <input type="file" id="file-upload" hidden>
            
            <textarea id="user-msg" placeholder="Nhập tin nhắn..." rows="1"></textarea>
            
            <button id="send-btn" class="send-btn">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="white"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path></svg>
            </button>
        </div>
    </div>
</div>
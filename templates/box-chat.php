<div id="ai-chat-window" class="ai-chat-container ht-agent__chat-window" style="display: none;">
    <div class="ht-agent__chat-header">
        <div class="ht-agent__header-info">
            <span class="ht-agent__status-dot"></span>
            <span class="ht-agent__chat-title">AI Assistant</span>
        </div>

        <div class="ht-agent__header-actions">
            <button id="create-new-session" class="ht-agent__create-session-btn" title="Tạo cuộc trò chuyện mới">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-circle-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                    <path d="M9 12h6" />
                    <path d="M12 9v6" />
                </svg>
            </button>
            <button id="close-chat" class="ht-agent__close-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </button>
        </div>

    </div>

    <div id="chat-content" class="ht-agent__chat-body">
        <div class="ht-agent__message ht-agent__ai-msg">
            <div class="ht-agent__msg-bubble">Chào bạn! Tôi có thể giúp gì cho bạn hôm nay?</div>
        </div>
    </div>

    <div class="ht-agent__chat-footer">
        <div class="ht-agent__input-wrapper">
            <label for="file-upload" class="ht-agent__attach-btn">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                    <path
                        d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48">
                    </path>
                </svg>
            </label>
            <input type="file" id="file-upload" hidden class="ht-agent__file-upload">

            <textarea id="user-msg" class="ht-agent__user-msg" placeholder="Nhập tin nhắn..." rows="1"></textarea>

            <button id="send-btn" class="ht-agent__send-btn">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="white">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
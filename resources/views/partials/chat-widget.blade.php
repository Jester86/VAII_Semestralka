<!-- Global Chat Widget -->
<div class="chat-widget" id="chat-widget">
    <button class="chat-toggle-btn" id="chat-toggle-btn" title="Global Chat">
        <span class="terminal-icon">&gt;_</span>
        <span class="chat-badge" id="chat-badge" style="display: none;">0</span>
    </button>

    <div class="chat-window" id="chat-window">
        <div class="chat-widget-header">
            <div class="terminal-title">
                <span class="terminal-dots">
                    <span class="dot red"></span>
                    <span class="dot yellow"></span>
                    <span class="dot green"></span>
                </span>
                <span class="terminal-name">global_chat.exe</span>
            </div>
            <div class="chat-header-actions">
                <span class="live-indicator">[LIVE]</span>
                <button class="chat-close-btn" id="chat-close-btn">x</button>
            </div>
        </div>

        <div class="chat-widget-messages" id="chat-messages"></div>

        @auth
        <div class="chat-widget-input">
            <form id="chat-form" class="chat-form">
                @csrf
                <span class="input-prompt">&gt;</span>
                <input type="text" id="chat-input" name="message" placeholder="Enter message..." maxlength="1000" autocomplete="off" required>
                <button type="submit" class="btn-send">SEND</button>
            </form>
        </div>
        @else
        <div class="chat-login-prompt">
            <span class="prompt-text">[ACCESS DENIED]</span> <a href="{{ route('login') }}">authenticate</a> to chat
        </div>
        @endauth
        <div class="scanline"></div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;700&display=swap');

.chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'JetBrains Mono', 'Courier New', monospace; }

.chat-toggle-btn {
    width: 56px; height: 56px; border-radius: 8px; background: #0a0a0a; border: 2px solid #00ff41;
    color: #00ff41; cursor: pointer; display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 20px rgba(0, 255, 65, 0.3), inset 0 0 20px rgba(0, 255, 65, 0.1);
    transition: all 0.3s; position: relative;
}
.chat-toggle-btn:hover { box-shadow: 0 0 30px rgba(0, 255, 65, 0.5), inset 0 0 30px rgba(0, 255, 65, 0.2); transform: scale(1.05); }
.chat-toggle-btn.active { background: #00ff41; color: #0a0a0a; }

.terminal-icon { font-size: 1.2rem; font-weight: 700; }

.chat-badge {
    position: absolute; top: -8px; right: -8px; background: #ff0040; color: #fff;
    font-size: 0.65rem; font-weight: bold; min-width: 20px; height: 20px; border-radius: 4px;
    display: flex; align-items: center; justify-content: center; padding: 0 5px;
    box-shadow: 0 0 10px rgba(255, 0, 64, 0.5);
}

.chat-window {
    position: absolute; bottom: 70px; right: 0; width: 380px; height: 500px;
    background: #0a0a0a; border-radius: 8px; border: 2px solid #00ff41;
    box-shadow: 0 0 40px rgba(0, 255, 65, 0.2); display: none; flex-direction: column; overflow: hidden;
}
.chat-window.open { display: flex; animation: terminalOpen 0.3s ease-out; }
.chat-window::before {
    content: ''; position: absolute; inset: 0; pointer-events: none; z-index: 1;
    background: repeating-linear-gradient(0deg, rgba(0, 255, 65, 0.03) 0px, rgba(0, 255, 65, 0.03) 1px, transparent 1px, transparent 2px);
}

.scanline {
    position: absolute; top: 0; left: 0; right: 0; height: 4px; pointer-events: none; z-index: 2;
    background: linear-gradient(to bottom, rgba(0, 255, 65, 0.1), transparent);
    animation: scanline 8s linear infinite;
}

@keyframes scanline { 0% { top: 0; } 100% { top: 100%; } }
@keyframes terminalOpen { from { opacity: 0; transform: translateY(10px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0.3; } }
@keyframes msgSlideIn { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }

.chat-widget-header {
    background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%); padding: 10px 15px;
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 1px solid #00ff41; position: relative; z-index: 3;
}

.terminal-title { display: flex; align-items: center; gap: 10px; }
.terminal-dots { display: flex; gap: 5px; }
.terminal-dots .dot { width: 10px; height: 10px; border-radius: 50%; }
.terminal-dots .dot.red { background: #ff5f57; }
.terminal-dots .dot.yellow { background: #ffbd2e; }
.terminal-dots .dot.green { background: #28c840; }
.terminal-name { color: #00ff41; font-size: 0.8rem; font-weight: 600; }

.chat-header-actions { display: flex; align-items: center; gap: 12px; }
.live-indicator { color: #00ff41; font-size: 0.7rem; font-weight: 600; animation: blink 1s infinite; }

.chat-close-btn {
    background: transparent; border: 1px solid #00ff41; color: #00ff41; font-size: 1rem;
    cursor: pointer; width: 24px; height: 24px; display: flex; align-items: center;
    justify-content: center; border-radius: 4px; transition: all 0.2s;
}
.chat-close-btn:hover { background: #00ff41; color: #0a0a0a; }

.chat-widget-messages {
    flex: 1; overflow-y: auto; padding: 15px; display: flex; flex-direction: column;
    gap: 8px; background: transparent; position: relative; z-index: 3;
}
.chat-widget-messages::-webkit-scrollbar { width: 6px; }
.chat-widget-messages::-webkit-scrollbar-track { background: #0a0a0a; }
.chat-widget-messages::-webkit-scrollbar-thumb { background: #00ff41; border-radius: 3px; }

.chat-msg {
    display: flex; gap: 10px; padding: 8px 10px; background: rgba(0, 255, 65, 0.05);
    border-left: 2px solid #00ff41; border-radius: 0 4px 4px 0; position: relative; transition: all 0.2s;
}
.chat-msg:hover { background: rgba(0, 255, 65, 0.1); }
.chat-msg.own { border-left-color: #00d4ff; background: rgba(0, 212, 255, 0.05); }
.chat-msg.own:hover { background: rgba(0, 212, 255, 0.1); }
.chat-msg.new { animation: msgSlideIn 0.3s ease-out; }

.chat-msg-avatar {
    width: 28px; height: 28px; background: #0a0a0a; border: 1px solid #00ff41; border-radius: 4px;
    display: flex; align-items: center; justify-content: center; color: #00ff41;
    font-weight: bold; font-size: 0.75rem; flex-shrink: 0;
}
.chat-msg-avatar.admin { border-color: #ff0040; color: #ff0040; box-shadow: 0 0 10px rgba(255, 0, 64, 0.3); }

.chat-msg-body { flex: 1; min-width: 0; }
.chat-msg-header { display: flex; align-items: center; gap: 8px; margin-bottom: 2px; }
.chat-msg-user { font-weight: 600; color: #00ff41; text-decoration: none; font-size: 0.75rem; }
.chat-msg-user:hover { text-shadow: 0 0 10px rgba(0, 255, 65, 0.5); }
.chat-msg-user.admin { color: #ff0040; }
.chat-msg-badge { background: transparent; border: 1px solid #ff0040; color: #ff0040; font-size: 0.55rem; padding: 1px 5px; border-radius: 2px; font-weight: 600; }
.chat-msg-time { color: #444; font-size: 0.6rem; margin-left: auto; }
.chat-msg-text { color: #b0b0b0; font-size: 0.8rem; line-height: 1.4; word-wrap: break-word; }

.chat-msg-delete { position: absolute; top: 5px; right: 5px; background: transparent; border: none; color: #444; font-size: 0.9rem; cursor: pointer; opacity: 0; transition: all 0.2s; padding: 0 3px; }
.chat-msg:hover .chat-msg-delete { opacity: 1; }
.chat-msg-delete:hover { color: #ff0040; }

.chat-widget-input, .chat-login-prompt { padding: 12px; background: #0d0d0d; border-top: 1px solid #00ff41; position: relative; z-index: 3; }
.chat-form { display: flex; gap: 8px; align-items: center; }
.input-prompt { color: #00ff41; font-weight: 700; font-size: 1rem; }

#chat-input {
    flex: 1; padding: 8px 12px; border: 1px solid #333; border-radius: 4px;
    background: #0a0a0a; color: #00ff41; font-size: 0.8rem; font-family: 'JetBrains Mono', monospace;
}
#chat-input:focus { outline: none; border-color: #00ff41; box-shadow: 0 0 10px rgba(0, 255, 65, 0.2); }
#chat-input::placeholder { color: #444; }

.btn-send {
    padding: 8px 16px; background: transparent; border: 1px solid #00ff41; border-radius: 4px;
    color: #00ff41; cursor: pointer; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 600;
}
.btn-send:hover { background: #00ff41; color: #0a0a0a; box-shadow: 0 0 15px rgba(0, 255, 65, 0.4); }

.chat-login-prompt { text-align: center; color: #666; font-size: 0.8rem; padding: 15px; }
.chat-login-prompt .prompt-text { color: #ff0040; }
.chat-login-prompt a { color: #00ff41; text-decoration: none; font-weight: 600; }
.chat-login-prompt a:hover { text-shadow: 0 0 10px rgba(0, 255, 65, 0.5); }

.chat-empty { text-align: center; color: #444; padding: 40px 20px; font-size: 0.8rem; }
.chat-empty::before { content: '> '; color: #00ff41; }

/* Responsive */
@media (max-width: 768px) {
    .chat-widget { bottom: 15px; right: 15px; }
    .chat-toggle-btn { width: 50px; height: 50px; }
    .terminal-icon { font-size: 1rem; }
    .chat-window { width: calc(100vw - 30px); height: calc(100vh - 100px); max-height: 600px; bottom: 65px; }
    .chat-widget-header { padding: 8px 12px; }
    .terminal-name { font-size: 0.7rem; }
    .terminal-dots .dot { width: 8px; height: 8px; }
    .chat-widget-messages { padding: 10px; }
    .chat-msg { padding: 6px 8px; gap: 8px; }
    .chat-msg-avatar { width: 24px; height: 24px; font-size: 0.65rem; }
    .chat-msg-user, .chat-msg-text { font-size: 0.7rem; }
    .chat-widget-input { padding: 10px; }
    #chat-input { padding: 6px 10px; font-size: 0.75rem; }
    .btn-send { padding: 6px 12px; font-size: 0.7rem; }
}

@media (max-width: 480px) {
    .chat-widget { bottom: 10px; right: 10px; }
    .chat-toggle-btn { width: 46px; height: 46px; }
    .chat-window { position: fixed; width: calc(100vw - 20px); height: calc(100vh - 80px); max-height: none; bottom: 66px; right: 10px; left: 10px; }
    .terminal-dots { display: none; }
    .chat-login-prompt { padding: 12px; font-size: 0.75rem; }
}

@media (max-width: 360px) {
    .chat-window { width: 100vw; height: calc(100vh - 70px); bottom: 60px; right: 0; left: 0; border-radius: 8px 8px 0 0; border-left: none; border-right: none; border-bottom: none; }
    .chat-toggle-btn { width: 44px; height: 44px; border-radius: 6px; }
}

@media (min-width: 1200px) { .chat-window { width: 420px; height: 550px; } }
@media (min-width: 1600px) {
    .chat-widget { bottom: 30px; right: 30px; }
    .chat-toggle-btn { width: 60px; height: 60px; }
    .chat-window { width: 450px; height: 600px; bottom: 80px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = id => document.getElementById(id);
    const chatToggleBtn = $('chat-toggle-btn'), chatWindow = $('chat-window'), chatCloseBtn = $('chat-close-btn');
    const chatMessages = $('chat-messages'), chatForm = $('chat-form'), chatInput = $('chat-input'), chatBadge = $('chat-badge');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let lastMessageId = 0, isPolling = true, isOpen = false, unreadCount = 0, isInitialLoad = true;
    const currentUserId = {{ auth()->id() ?? 'null' }};
    const currentUserRole = '{{ auth()->user()->role ?? "guest" }}';

    function toggleChat(open) {
        isOpen = open;
        chatWindow.classList.toggle('open', open);
        chatToggleBtn.classList.toggle('active', open);
        if (open) { unreadCount = 0; updateBadge(); scrollToBottom(); chatInput?.focus(); }
    }

    chatToggleBtn.addEventListener('click', () => toggleChat(!isOpen));
    chatCloseBtn.addEventListener('click', () => toggleChat(false));

    function updateBadge() {
        chatBadge.style.display = unreadCount > 0 && !isOpen ? 'flex' : 'none';
        chatBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
    }

    const scrollToBottom = () => chatMessages.scrollTop = chatMessages.scrollHeight;
    const isNearBottom = () => chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight < 50;
    const escapeHtml = text => { const div = document.createElement('div'); div.textContent = text; return div.innerHTML; };

    function fadeOutRemove(el) {
        if (!el) return;
        el.style.opacity = '0';
        el.style.transform = 'translateX(20px)';
        setTimeout(() => el.remove(), 200);
    }

    function createMessageElement(msg, isNew) {
        const isOwn = msg.user_id === currentUserId, isAdmin = msg.user_role === 'admin';
        const canDelete = currentUserId && (currentUserId === msg.user_id || currentUserRole === 'admin');
        const div = document.createElement('div');
        div.className = 'chat-msg' + (isOwn ? ' own' : '') + (isNew ? ' new' : '');
        div.dataset.messageId = msg.id;
        div.innerHTML = `<div class="chat-msg-avatar ${isAdmin ? 'admin' : ''}">${msg.username.charAt(0).toUpperCase()}</div>
            <div class="chat-msg-body">
                <div class="chat-msg-header">
                    <a href="/profile/${msg.user_id}" class="chat-msg-user ${isAdmin ? 'admin' : ''}">${escapeHtml(msg.username)}</a>
                    ${isAdmin ? '<span class="chat-msg-badge">ADMIN</span>' : ''}
                    <span class="chat-msg-time">${msg.time_ago}</span>
                </div>
                <div class="chat-msg-text">${msg.message}</div>
            </div>
            ${canDelete ? `<button class="chat-msg-delete" data-id="${msg.id}">x</button>` : ''}`;
        return div;
    }

    function removeDeletedMessages(serverMessageIds) {
        chatMessages.querySelectorAll('.chat-msg').forEach(el => {
            if (!serverMessageIds.includes(parseInt(el.dataset.messageId))) fadeOutRemove(el);
        });
    }

    async function loadMessages() {
        try {
            const data = await (await fetch('/chat/messages')).json();
            chatMessages.innerHTML = '';
            if (data.messages?.length) {
                data.messages.forEach(msg => {
                    chatMessages.appendChild(createMessageElement(msg, false));
                    lastMessageId = Math.max(lastMessageId, msg.id);
                });
                scrollToBottom();
            } else {
                chatMessages.innerHTML = '<div class="chat-empty">No messages yet. Start the conversation!</div>';
            }
            isInitialLoad = false;
        } catch (e) { console.error('Error loading messages:', e); }
    }

    async function pollMessages() {
        if (!isPolling) return;
        try {
            const data = await (await fetch('/chat/messages?after_id=' + lastMessageId)).json();
            if (data.message_ids) removeDeletedMessages(data.message_ids);
            if (data.messages?.length) {
                const wasNearBottom = isNearBottom();
                chatMessages.querySelector('.chat-empty')?.remove();
                data.messages.forEach(msg => {
                    if (!chatMessages.querySelector(`[data-message-id="${msg.id}"]`)) {
                        chatMessages.appendChild(createMessageElement(msg, true));
                        lastMessageId = Math.max(lastMessageId, msg.id);
                        if (!isOpen && msg.user_id !== currentUserId && !isInitialLoad) { unreadCount++; updateBadge(); }
                    }
                });
                if (wasNearBottom || isOpen) scrollToBottom();
            }
        } catch (e) { console.error('Error polling messages:', e); }
        setTimeout(pollMessages, 2000);
    }

    chatForm?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message) return;
        chatInput.disabled = true;
        try {
            const data = await (await fetch('/chat/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ message })
            })).json();
            if (data.success && data.message) {
                chatMessages.querySelector('.chat-empty')?.remove();
                if (!chatMessages.querySelector(`[data-message-id="${data.message.id}"]`)) {
                    chatMessages.appendChild(createMessageElement(data.message, true));
                    lastMessageId = Math.max(lastMessageId, data.message.id);
                }
                scrollToBottom();
                chatInput.value = '';
            }
        } catch (e) { console.error('Error sending message:', e); }
        chatInput.disabled = false;
        chatInput.focus();
    });

    chatMessages.addEventListener('click', async function(e) {
        if (!e.target.classList.contains('chat-msg-delete')) return;
        if (!confirm('Delete this message?')) return;
        const messageId = e.target.dataset.id;
        try {
            const data = await (await fetch('/chat/messages/' + messageId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })).json();
            if (data.success) fadeOutRemove(chatMessages.querySelector(`[data-message-id="${messageId}"]`));
        } catch (e) { console.error('Error deleting message:', e); }
    });

    loadMessages();
    setTimeout(pollMessages, 2000);
    window.addEventListener('beforeunload', () => isPolling = false);
});
</script>

@extends('theme::layouts.app', ['title' => 'AI Agent · '.$businessLabel, 'heading' => 'AI Agent', 'chatWorkspace' => true])

@section('content')
<div class="aibot-root" data-business-name="{{ e($businessLabel) }}">
    <div class="aibot-main">
        <div class="aibot-thread" id="aibot-thread" tabindex="0" aria-live="polite">
            <div class="aibot-welcome" id="aibot-welcome">
                <div class="aibot-welcome-icon" aria-hidden="true">
                    <i class="fa fa-sparkles"></i>
                </div>
                <h1 class="aibot-welcome-title">How can I help today?</h1>
                <p class="aibot-welcome-sub muted">Ask about ledgers, HR, rentals, or daily operations. Context: <strong>{{ $businessLabel }}</strong>@if(!$business)<span> — choose a business in the header when you’re ready for company-specific answers.</span>@endif Responses are placeholders until your model is connected.</p>
                <div class="aibot-suggestions">
                    <button type="button" class="aibot-chip" data-prompt="Summarize cash position for my business">Summarize cash position</button>
                    <button type="button" class="aibot-chip" data-prompt="What HR records should I maintain?">HR compliance tips</button>
                    <button type="button" class="aibot-chip" data-prompt="Explain SociBiz Overview tiles">Explain Overview</button>
                </div>
            </div>
            <div class="aibot-messages" id="aibot-messages" hidden></div>
        </div>

        <div class="aibot-composer-wrap">
            <form class="aibot-composer" id="aibot-form" autocomplete="off">
                @csrf
                <label class="sr-only" for="aibot-input">Message</label>
                <textarea id="aibot-input" class="aibot-input" rows="1" placeholder="Message AI Agent…" maxlength="32000"></textarea>
                <button type="submit" class="aibot-send" id="aibot-send" aria-label="Send message">
                    <i class="fa fa-arrow-up"></i>
                </button>
            </form>
            <p class="aibot-disclaimer muted">AI can make mistakes. Verify important finance or compliance answers.</p>
        </div>
    </div>

    <aside class="aibot-panel-nav" aria-label="Conversations">
        <button type="button" class="aibot-new-chat" id="aibot-new-chat">
            <i class="fa fa-plus"></i><span>New chat</span>
        </button>
        <div class="aibot-history-label muted">Recent</div>
        <ul class="aibot-history-list" id="aibot-history">
            <li><button type="button" class="aibot-history-item is-active"><i class="fa fa-comment"></i><span class="truncate">Workspace assistant</span></button></li>
        </ul>
        <div class="aibot-panel-foot muted">
            <small>{{ $businessLabel }}</small>
        </div>
    </aside>
</div>

<style>
    .aibot-root{display:flex;flex:1;min-height:0;min-height:calc(100vh - 140px);}
    .aibot-panel-nav{
        width:260px;
        flex-shrink:0;
        border-left:1px solid var(--border);
        background:color-mix(in srgb,var(--card) 94%,transparent);
        display:flex;
        flex-direction:column;
        gap:12px;
        padding:14px;
    }
    .aibot-new-chat{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        width:100%;
        padding:11px 12px;
        border-radius:12px;
        border:1px solid var(--border);
        background:color-mix(in srgb,var(--card) 88%,transparent);
        color:var(--text);
        font-weight:600;
        font-size:13px;
        cursor:pointer;
        transition:border-color .2s,background .2s;
    }
    .aibot-new-chat:hover{border-color:color-mix(in srgb,var(--primary) 50%,var(--border));background:color-mix(in srgb,var(--primary) 10%,transparent);}
    .aibot-history-label{font-size:11px;text-transform:uppercase;letter-spacing:.06em;margin:4px 0 0;padding:0 4px;}
    .aibot-history-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:4px;}
    .aibot-history-item{
        width:100%;
        display:flex;
        align-items:center;
        gap:8px;
        padding:9px 10px;
        border:none;
        border-radius:10px;
        background:transparent;
        color:var(--text);
        font-size:13px;
        text-align:left;
        cursor:pointer;
        transition:background .15s;
    }
    .aibot-history-item:hover{background:color-mix(in srgb,var(--primary) 8%,transparent);}
    .aibot-history-item.is-active{background:color-mix(in srgb,var(--primary) 14%,transparent);outline:1px solid color-mix(in srgb,var(--primary) 35%,var(--border));}
    .aibot-history-item i{font-size:12px;color:var(--muted);width:1em;text-align:center;}
    .truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .aibot-panel-foot{margin-top:auto;padding-top:8px;}
    .aibot-main{display:flex;flex-direction:column;flex:1;min-width:0;min-height:0;}
    .aibot-thread{flex:1;overflow-y:auto;overflow-x:hidden;padding:28px 24px 16px;display:flex;flex-direction:column;}
    .aibot-welcome{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:24px 12px 48px;max-width:520px;margin:0 auto;width:100%;}
    .aibot-welcome-icon{
        width:52px;height:52px;border-radius:16px;
        background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 45%,transparent),color-mix(in srgb,var(--primary) 15%,transparent));
        display:grid;place-items:center;margin-bottom:16px;color:var(--primary);font-size:22px;border:1px solid color-mix(in srgb,var(--primary) 28%,var(--border));
    }
    .aibot-welcome-title{margin:0 0 8px;font-size:clamp(1.35rem,2.5vw,1.65rem);font-weight:680;letter-spacing:-.02em;}
    .aibot-welcome-sub{margin:0 0 20px;line-height:1.5;font-size:14px;}
    .aibot-suggestions{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;}
    .aibot-chip{
        padding:9px 14px;border-radius:999px;font-size:12px;font-weight:500;
        border:1px solid var(--border);background:color-mix(in srgb,var(--card) 85%,transparent);
        color:var(--text);cursor:pointer;transition:all .18s ease;
    }
    .aibot-chip:hover{border-color:color-mix(in srgb,var(--primary) 45%,var(--border));background:color-mix(in srgb,var(--primary) 10%,transparent);}
    .aibot-messages{display:flex;flex-direction:column;gap:14px;max-width:780px;margin:0 auto;width:100%;padding-bottom:24px;}
    .aibot-row{display:flex;gap:10px;width:100%;animation:aibot-enter .26s ease;}
    .aibot-row-user{justify-content:flex-end;}
    .aibot-row-ai{justify-content:flex-start;}
    @keyframes aibot-enter{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:none;}}
    .aibot-avatar{width:34px;height:34px;border-radius:10px;flex-shrink:0;display:grid;place-items:center;font-size:14px;background:color-mix(in srgb,var(--primary) 16%,transparent);border:1px solid color-mix(in srgb,var(--primary) 30%,var(--border));color:var(--primary);}
    .aibot-row-user .aibot-avatar{background:linear-gradient(135deg,var(--btn-bg),color-mix(in srgb,var(--btn-bg) 55%,var(--border)));border-color:transparent;color:#fff;}
    .aibot-msg{max-width:min(560px,calc(100% - 48px));padding:12px 16px;border-radius:16px;font-size:14px;line-height:1.52;white-space:pre-wrap;word-break:break-word;}
    .aibot-msg-user{
        border-bottom-right-radius:6px;background:linear-gradient(135deg,color-mix(in srgb,var(--primary) 32%,transparent),color-mix(in srgb,var(--primary) 12%,transparent));
        border:1px solid color-mix(in srgb,var(--primary) 42%,var(--border));
        color:var(--text);
    }
    .aibot-msg-ai{
        border-bottom-left-radius:6px;background:color-mix(in srgb,var(--card) 96%,#000);border:1px solid var(--border);color:var(--text);margin-left:0;
    }
    .aibot-composer-wrap{flex-shrink:0;padding:14px 20px 20px;background:linear-gradient(180deg,transparent,color-mix(in srgb,var(--bg) 70%,transparent) 35%);}
    .aibot-composer{display:flex;align-items:flex-end;gap:10px;max-width:780px;margin:0 auto;background:color-mix(in srgb,var(--card) 95%,transparent);border:1px solid var(--border);border-radius:18px;padding:10px 10px 10px 16px;}
    .aibot-input{
        flex:1;
        resize:none;border:none;background:transparent;color:var(--text);
        font-size:15px;line-height:1.45;max-height:180px;min-height:24px;padding:8px 0;font-family:inherit;
        outline:none;
    }
    .aibot-send{
        width:40px;height:40px;border-radius:12px;border:none;flex-shrink:0;
        background:var(--btn-bg);color:#fff;cursor:pointer;display:grid;place-items:center;transition:transform .15s,background .15s;
    }
    .aibot-send:hover{background:var(--btn-hover);color:#111827;}
    .aibot-send:active{transform:scale(.96);}
    .aibot-disclaimer{text-align:center;font-size:11px;margin:8px 0 0;}
    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;}
    .aibot-typing{display:inline-flex;gap:4px;align-items:center;padding:4px 0;}
    .aibot-typing span{width:6px;height:6px;border-radius:50%;background:var(--muted);animation:aibot-dot 1.2s ease-in-out infinite;}
    .aibot-typing span:nth-child(2){animation-delay:.2s;}
    .aibot-typing span:nth-child(3){animation-delay:.4s;}
    @keyframes aibot-dot{0%,80%,100%{opacity:.35;transform:scale(.88);}40%{opacity:1;transform:scale(1);}}
    @media (max-width:900px){
        .aibot-root{flex-direction:column;min-height:min(70vh,560px);}
        .aibot-panel-nav{width:100%;flex-direction:row;flex-wrap:wrap;align-items:center;border-left:0;border-top:1px solid var(--border);}
        .aibot-new-chat{width:auto;flex:1;min-width:140px;}
        .aibot-history-label{display:none;}
        .aibot-history-list{flex-direction:row;flex:1;overflow-x:auto;}
        .aibot-history-item{white-space:nowrap;}
        .aibot-panel-foot{display:none;}
    }
</style>

<script>
(function () {
    const root = document.querySelector('.aibot-root');
    if (!root) return;
    const businessName = root.dataset.businessName || 'your business';
    const welcome = document.getElementById('aibot-welcome');
    const messagesEl = document.getElementById('aibot-messages');
    const thread = document.getElementById('aibot-thread');
    const form = document.getElementById('aibot-form');
    const input = document.getElementById('aibot-input');
    const newChatBtn = document.getElementById('aibot-new-chat');

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text;
        return d.innerHTML;
    }

    function hideWelcomeIfNeeded() {
        if (!messagesEl.children.length) return;
        welcome.hidden = true;
        messagesEl.hidden = false;
    }

    function showWelcome() {
        welcome.hidden = false;
        messagesEl.hidden = true;
        messagesEl.innerHTML = '';
        thread.scrollTop = 0;
        input.focus();
    }

    function appendUserBubble(text) {
        const row = document.createElement('div');
        row.className = 'aibot-row aibot-row-user';
        row.innerHTML =
            '<div class="aibot-msg aibot-msg-user">' + escapeHtml(text) + '</div>' +
            '<div class="aibot-avatar" aria-hidden="true"><i class="fa fa-user"></i></div>';
        messagesEl.appendChild(row);
        hideWelcomeIfNeeded();
        thread.scrollTop = thread.scrollHeight;
    }

    function appendTypingRow() {
        const row = document.createElement('div');
        row.className = 'aibot-row aibot-row-ai';
        row.dataset.role = 'typing';
        row.innerHTML =
            '<div class="aibot-avatar" aria-hidden="true"><i class="fa fa-robot"></i></div>' +
            '<div class="aibot-msg aibot-msg-ai"><span class="aibot-typing" aria-hidden="true"><span></span><span></span><span></span></span></div>';
        messagesEl.appendChild(row);
        thread.scrollTop = thread.scrollHeight;
        return row;
    }

    function replaceTypingWithAi(text, typingRow) {
        const bubble = typingRow.querySelector('.aibot-msg-ai');
        if (bubble) bubble.innerHTML = escapeHtml(text);
        typingRow.removeAttribute('data-role');
        thread.scrollTop = thread.scrollHeight;
    }

    function fakeAssistantReply(prompt) {
        return (
            'Here is a scaffold reply for «' +
            prompt.slice(0, 120) +
            (prompt.length > 120 ? '…' : '') +
            '».\n\n' +
            'Connect your LLM or rules engine on the server to replace this bubble. Workspace context is available as ' +
            businessName +
            '.'
        );
    }

    function resizeInput() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 180) + 'px';
    }

    async function submitPrompt(text) {
        const trimmed = text.trim();
        if (!trimmed) return;
        appendUserBubble(trimmed);
        input.value = '';
        resizeInput();
        const typingRow = appendTypingRow();
        await new Promise(function (resolve) {
            window.setTimeout(resolve, 500 + Math.random() * 500);
        });
        replaceTypingWithAi(fakeAssistantReply(trimmed), typingRow);
    }

    input.addEventListener('input', resizeInput);

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitPrompt(input.value);
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            submitPrompt(input.value);
        }
    });

    document.querySelectorAll('.aibot-chip').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const p = btn.getAttribute('data-prompt') || '';
            if (p) submitPrompt(p);
        });
    });

    newChatBtn.addEventListener('click', showWelcome);

    resizeInput();
})();
</script>
@endsection

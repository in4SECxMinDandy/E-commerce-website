{--
    File frontend: chat/index.blade.php
    Chuc nang: Man hinh chat chinh phia nguoi dung/khach, render lich su tin nhan, form gui noi dung va upload anh.
    Vai tro giao dien: Ket noi session chat voi backend qua data-attribute de phuc vu fetch, polling fallback va realtime qua Echo.
    Tuong tac: Inline script xu ly tai tin nhan, upload anh, gui tin moi, vo hieu hoa form khi session dong va tu dong cap nhat giao dien.
--}}
@extends('layouts.base')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <div class="card border-0 shadow-sm rounded-4" data-chat-root
                 data-session-id="{{ $chatSession->id }}"
                 data-channel="{{ $channelName }}"
                 data-status="{{ $chatSession->status->value }}"
                 data-messages-url="{{ route('chat.messages') }}"
                 data-send-url="{{ route('chat.send') }}"
                 data-upload-url="{{ route('chat.upload-image') }}"
                 data-poll-seconds="{{ $pollingSeconds }}">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-secondary">Chat session #{{ $chatSession->id }}</div>
                        <div class="fw-semibold">
                            {{ $guestName ?: auth()->user()?->full_name ?: 'KhÃ¡ch hÃ ng' }}
                        </div>
                    </div>
                    <span class="badge text-bg-{{ $chatSession->status->value === 'open' ? 'success' : 'secondary' }}" data-chat-status>
                        {{ strtoupper($chatSession->status->value) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="chat-thread rounded-4 border p-3 mb-4" data-chat-messages>
                        @forelse ($messages as $message)
                            <div class="chat-bubble {{ $message->sender_role->value === 'admin' ? 'chat-bubble-admin' : 'chat-bubble-user' }}">
                                <div class="small fw-semibold mb-1">{{ $message->sender_role->label() }}</div>
                                @if ($message->content)
                                    <div>{{ $message->content }}</div>
                                @endif
                                @if ($message->image_path)
                                    <img src="{{ Storage::disk('public')->url($message->image_path) }}" alt="Chat image" class="img-fluid rounded-3 mt-2">
                                @endif
                                <div class="small text-secondary mt-2">{{ optional($message->created_at)->format('d/m/Y H:i') }}</div>
                            </div>
                        @empty
                            <div class="text-secondary">ChÆ°a cÃ³ tin nháº¯n nÃ o. HÃ£y gá»­i tin Ä‘áº§u tiÃªn.</div>
                        @endforelse
                    </div>

                    <form class="vstack gap-3" data-chat-form>
                        <div>
                            <label class="form-label" for="chat_content">Ná»™i dung</label>
                            <textarea class="form-control" id="chat_content" rows="3" placeholder="Nháº­p tin nháº¯n..."></textarea>
                        </div>
                        <div>
                            <label class="form-label" for="chat_image">áº¢nh Ä‘Ã­nh kÃ¨m</label>
                            <input class="form-control" id="chat_image" type="file" accept="image/*">
                        </div>
                        <button class="btn btn-dark align-self-start" type="submit">Gá»­i tin nháº¯n</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.querySelector('[data-chat-root]');

            if (!root) {
                return;
            }

            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const messagesBox = root.querySelector('[data-chat-messages]');
            const form = root.querySelector('[data-chat-form]');
            const textarea = form.querySelector('#chat_content');
            const fileInput = form.querySelector('#chat_image');
            const submitButton = form.querySelector('button[type="submit"]');
            const statusBadge = root.querySelector('[data-chat-status]');
            const sessionId = root.dataset.sessionId;
            const channelName = root.dataset.channel;
            const emptyMessage = 'ChÆ°a cÃ³ tin nháº¯n nÃ o. HÃ£y gá»­i tin Ä‘áº§u tiÃªn.';

            const updateStatus = (status) => {
                statusBadge.textContent = String(status).toUpperCase();
                statusBadge.className = `badge ${status === 'open' ? 'text-bg-success' : 'text-bg-secondary'}`;
            };

            const disableChat = (message = 'PhiÃªn chat Ä‘Ã£ háº¿t háº¡n hoáº·c bá»‹ vÃ´ hiá»‡u hÃ³a.') => {
                updateStatus('closed');
                textarea.disabled = true;
                fileInput.disabled = true;
                submitButton.disabled = true;

                if (!root.querySelector('[data-chat-expired]')) {
                    const notice = document.createElement('div');
                    notice.className = 'alert alert-warning mt-3 mb-0';
                    notice.dataset.chatExpired = 'true';
                    notice.textContent = message;
                    form.appendChild(notice);
                }
            };

            const createMessageNode = (message) => {
                const bubble = document.createElement('div');
                bubble.className = `chat-bubble ${message.sender_role === 'admin' ? 'chat-bubble-admin' : 'chat-bubble-user'}`;

                const sender = document.createElement('div');
                sender.className = 'small fw-semibold mb-1';
                sender.textContent = message.sender_label;
                bubble.appendChild(sender);

                if (message.content) {
                    const content = document.createElement('div');
                    content.textContent = message.content;
                    bubble.appendChild(content);
                }

                if (message.image_url) {
                    const image = document.createElement('img');
                    image.src = message.image_url;
                    image.alt = 'Chat image';
                    image.className = 'img-fluid rounded-3 mt-2';
                    bubble.appendChild(image);
                }

                const createdAt = document.createElement('div');
                createdAt.className = 'small text-secondary mt-2';
                createdAt.textContent = message.created_at ? new Date(message.created_at).toLocaleString() : '';
                bubble.appendChild(createdAt);

                return bubble;
            };

            const renderMessages = (messages) => {
                messagesBox.replaceChildren();

                if (!messages.length) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'text-secondary';
                    emptyState.textContent = emptyMessage;
                    messagesBox.appendChild(emptyState);
                    return;
                }

                const fragment = document.createDocumentFragment();

                messages.forEach((message) => {
                    fragment.appendChild(createMessageNode(message));
                });

                messagesBox.appendChild(fragment);
                messagesBox.scrollTop = messagesBox.scrollHeight;
            };

            const fetchMessages = async () => {
                try {
                    const response = await fetch(`${root.dataset.messagesUrl}?session_id=${sessionId}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });

                    if (response.status === 403) {
                        disableChat();
                        return;
                    }

                    if (!response.ok) {
                        return;
                    }

                    const payload = await response.json();
                    renderMessages(payload.data);
                    updateStatus(payload.status);

                    if (payload.status !== 'open') {
                        disableChat();
                    }
                } catch (_) {
                    // Polling fallback keeps the conversation updated if websocket is unavailable.
                }
            };

            const sendMessage = async (content, imagePath = null) => {
                const response = await fetch(root.dataset.sendUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ session_id: sessionId, content, image_path: imagePath }),
                });

                if (response.status === 403) {
                    disableChat();
                    return;
                }

                if (!response.ok) {
                    await fetchMessages();
                    return;
                }

                textarea.value = '';
                fileInput.value = '';
                await fetchMessages();
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (submitButton.disabled) {
                    return;
                }

                let imagePath = null;

                if (fileInput.files.length) {
                    const formData = new FormData();
                    formData.append('session_id', sessionId);
                    formData.append('image', fileInput.files[0]);

                    const uploadResponse = await fetch(root.dataset.uploadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    });

                    if (uploadResponse.status === 403) {
                        disableChat();
                        return;
                    }

                    if (uploadResponse.ok) {
                        const uploadPayload = await uploadResponse.json();
                        imagePath = uploadPayload.path;
                    }
                }

                await sendMessage(textarea.value.trim(), imagePath);
            });

            if (window.Echo) {
                window.Echo.channel(channelName)
                    .listen('.chat.message.created', fetchMessages)
                    .listen('.chat.session.status', (event) => {
                        updateStatus(event.status);

                        if (event.status !== 'open') {
                            disableChat();
                        }
                    });
            }

            fetchMessages();
            setInterval(fetchMessages, Number(root.dataset.pollSeconds) * 1000);
        });
    </script>
@endsection

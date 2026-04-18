{{--
    File frontend: admin/chat/index.blade.php
    Chuc nang: Giao dien quan ly chat danh cho admin, gom cot trai chon session va cot phai xem noi dung, phan hoi hoac dong session.
    Vai tro giao dien: Ho tro admin theo doi hoi thoai giua khach va he thong, xoa tin, xoa session va xu ly realtime.
    Tuong tac: Co inline script lang nghe Laravel Echo, dong session bang fetch Ajax, va reload giao dien khi co su kien chat moi.
--}}
@extends('layouts.admin')

@section('content')
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="p-4 border-bottom">
                        <h1 class="h4 mb-1">Phiên chat</h1>
                        <p class="text-secondary mb-0">Danh sách session đang mở hoặc đã đóng.</p>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($sessions as $session)
                            <a class="list-group-item list-group-item-action {{ $selectedSession && $selectedSession->id === $session->id ? 'active' : '' }}"
                               href="{{ route('admin.chat.index', ['session' => $session->id]) }}">
                                <div class="fw-semibold">{{ $session->guest_name ?: $session->user?->full_name ?: 'Người dùng #'.$session->user_id }}</div>
                                <div class="small opacity-75">{{ $session->messages_count }} tin nhắn • {{ strtoupper($session->status->value) }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-4">{{ $sessions->links() }}</div>
        </div>

        <div class="col-xl-8">
            @if ($selectedSession)
                <div class="card border-0 shadow-sm rounded-4"
                     data-admin-chat-root
                     data-close-url="{{ route('admin.chat.close', $selectedSession) }}"
                     data-channel="chat.session.{{ $selectedSession->id }}.{{ $selectedSession->visit_token }}">
                    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-secondary">Session #{{ $selectedSession->id }}</div>
                            <div class="fw-semibold">{{ $selectedSession->guest_name ?: $selectedSession->user?->full_name ?: 'Khách' }}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-close-session>Đóng session</button>
                            <form method="POST" action="{{ route('admin.chat.destroy', $selectedSession) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" type="submit">Xóa session</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="chat-thread rounded-4 border p-3 mb-4" data-chat-messages>
                            @foreach ($selectedSession->messages as $message)
                                <div class="chat-bubble {{ $message->sender_role->value === 'admin' ? 'chat-bubble-admin' : 'chat-bubble-user' }}">
                                    <div class="small fw-semibold mb-1">{{ $message->sender_role->label() }}</div>
                                    @if ($message->content)
                                        <div>{{ $message->content }}</div>
                                    @endif
                                    @if ($message->image_path)
                                        <img src="{{ Storage::disk('public')->url($message->image_path) }}" alt="Chat image" class="img-fluid rounded-3 mt-2">
                                    @endif
                                    <div class="small text-secondary mt-2">{{ optional($message->created_at)->format('d/m/Y H:i') }}</div>
                                    <form method="POST" action="{{ route('admin.chat.messages.destroy', [$selectedSession, $message]) }}" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0" type="submit">Xóa tin</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('admin.chat.reply', $selectedSession) }}" class="vstack gap-3">
                            @csrf
                            <div>
                                <label class="form-label" for="content">Phản hồi admin</label>
                                <textarea class="form-control" id="content" name="content" rows="3"></textarea>
                            </div>
                            <button class="btn btn-dark align-self-start" type="submit">Gửi phản hồi</button>
                        </form>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const root = document.querySelector('[data-admin-chat-root]');

                        if (!root) {
                            return;
                        }

                        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const closeButton = root.querySelector('[data-close-session]');

                        closeButton.addEventListener('click', async () => {
                            await fetch(root.dataset.closeUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrf,
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });

                            window.location.reload();
                        });

                        if (window.Echo) {
                            window.Echo.channel(root.dataset.channel)
                                .listen('.chat.message.created', () => window.location.reload())
                                .listen('.chat.session.status', () => window.location.reload());
                        }
                    });
                </script>
            @else
                <div class="alert alert-secondary mb-0">Chọn một session bên trái để xem nội dung chat.</div>
            @endif
        </div>
    </div>
@endsection


<div class="hms-chat-panel">
    @if($selected_conversation)
        @php
            $receiverName = optional($receviverUser)->name ?? 'محادثة';
            $receiverInitial = mb_substr($receiverName, 0, 1, 'UTF-8');
            $receiverAvatar = null;
            if ($receviverUser instanceof \App\Models\Doctor && optional($receviverUser->image)->filename) {
                $receiverAvatar = URL::asset('Dashboard/img/doctors/' . $receviverUser->image->filename);
            } elseif ($receviverUser instanceof \App\Models\Patient) {
                $receiverAvatar = URL::asset('Dashboard/img/faces/default-avatar.png');
            }
        @endphp
        <div class="hms-chat-panel__inner" wire:poll.3s="refreshMessages">
            <div class="hms-chat-header">
                <div class="hms-chat-header__user">
                    <div class="hms-chat-avatar hms-chat-avatar--lg">
                        @if($receiverAvatar)
                            <img src="{{ $receiverAvatar }}" alt="{{ $receiverName }}">
                        @else
                            <span>{{ $receiverInitial }}</span>
                        @endif
                    </div>
                    <div class="hms-chat-header__meta">
                        <h6>{{ $receiverName }}</h6>
                        <small>محادثة نشطة</small>
                    </div>
                </div>
            </div>

            <div class="hms-chat-body" id="ChatBody">
                <div class="hms-chat-messages" id="ChatMessages">
                    @forelse($messages as $message)
                        @php $isMine = $auth_email === $message->sender_email; @endphp
                        <div class="hms-chat-msg {{ $isMine ? 'hms-chat-msg--mine' : 'hms-chat-msg--theirs' }}" wire:key="msg-{{ $message->id }}">
                            @unless($isMine)
                                <div class="hms-chat-avatar hms-chat-avatar--sm">
                                    @if($receiverAvatar)
                                        <img src="{{ $receiverAvatar }}" alt="">
                                    @else
                                        <span>{{ $receiverInitial }}</span>
                                    @endif
                                </div>
                            @endunless
                            <div class="hms-chat-bubble-wrap">
                                <div class="hms-chat-bubble">{{ $message->body }}</div>
                                <time>{{ optional($message->created_at)->format('H:i') }}</time>
                            </div>
                        </div>
                    @empty
                        <div class="hms-chat-empty-thread">
                            <i class="far fa-comment-dots"></i>
                            <p>ابدأ المحادثة بإرسال أول رسالة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @else
        <div class="hms-chat-placeholder">
            <div class="hms-chat-placeholder__icon"><i class="far fa-comments"></i></div>
            <h5>اختر محادثة</h5>
            <p>حدّد محادثة من القائمة لعرض الرسائل والرد</p>
        </div>
    @endif
</div>

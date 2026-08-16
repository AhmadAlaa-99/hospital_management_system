<div class="hms-chat-sidebar">
    <div class="main-chat-list hms-chat-list" id="ChatList" wire:poll.5s>
        @forelse($conversations as $conversation)
            @php
                $other = $this->resolveUser($conversation);
                $lastMessage = $conversation->messages->first();
                $isSelected = $selected_conversation && (int) $selected_conversation->id === (int) $conversation->id;
                $otherInitial = $other ? mb_substr($other->name, 0, 1, 'UTF-8') : '?';
                $otherAvatar = null;
                if ($other instanceof \App\Models\Doctor && optional($other->image)->filename) {
                    $otherAvatar = URL::asset('Dashboard/img/doctors/' . $other->image->filename);
                } elseif ($other instanceof \App\Models\Patient) {
                    $otherAvatar = URL::asset('Dashboard/img/faces/default-avatar.png');
                }
            @endphp
            @if($other)
                <div class="media hms-chat-list-item {{ $isSelected ? 'selected' : 'new' }}"
                     wire:click="chatUserSelected({{ $conversation->id }}, {{ $other->id }})">
                    <div class="hms-chat-avatar hms-chat-avatar--md">
                        @if($otherAvatar)
                            <img src="{{ $otherAvatar }}" alt="{{ $other->name }}">
                        @else
                            <span>{{ $otherInitial }}</span>
                        @endif
                    </div>
                    <div class="media-body">
                        <div class="media-contact-name">
                            <span>{{ $other->name }}</span>
                            <span>{{ $lastMessage ? $lastMessage->created_at->diffForHumans() : '' }}</span>
                        </div>
                        <p>{{ $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->body, 48) : 'لا توجد رسائل بعد' }}</p>
                    </div>
                </div>
            @endif
        @empty
            <div class="hms-chat-list-empty">
                <i class="far fa-comment-alt"></i>
                <p>لا توجد محادثات بعد</p>
            </div>
        @endforelse
    </div>
</div>

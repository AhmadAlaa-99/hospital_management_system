<div class="hms-chat-composer-wrap">
    @if($selected_conversation)
        <form wire:submit.prevent="sendMessage" class="hms-chat-composer">
            <div class="hms-chat-composer__field">
                <input class="form-control"
                       wire:model.defer="body"
                       placeholder="اكتب رسالتك..."
                       type="text"
                       autocomplete="off"
                       maxlength="2000"
                       wire:keydown.enter.prevent="sendMessage">
                <button class="hms-chat-composer__send" type="submit" title="إرسال"
                        wire:loading.attr="disabled"
                        wire:target="sendMessage">
                    <span wire:loading.remove wire:target="sendMessage">
                        <i class="fas fa-paper-plane"></i>
                    </span>
                    <span wire:loading wire:target="sendMessage">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>

    @foreach ($message_list as $index => $message_item)
        @if ($message_item->sender_id == $auth_user->id)
            <div class="chat-item chat-right" style="">
                <img src="https://www.gravatar.com/avatar/75d23af433e0cea4c0e45a56dba18b30">
                <div class="chat-details">
                    <div class="chat-text">{{ html_decode($message_item->message) }}</div>
                    <div class="chat-time">{{ $message_item->created_at->format('d F, Y, H:i A') }}</div>
                </div>
            </div>
        @else
            <div class="chat-item chat-left" style="">
                <img src="https://www.gravatar.com/avatar/75d23af433e0cea4c0e45a56dba18b30">
                <div class="chat-details">
                    <div class="chat-text">{{ html_decode($message_item->message) }}</div>
                    <div class="chat-time">{{ $message_item->created_at->format('d F, Y, H:i A') }}</div>
                </div>
            </div>
        @endif

    @endforeach

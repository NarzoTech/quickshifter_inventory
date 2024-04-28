    <div class="card-header">
        <h4>Chat with {{ $contact_author->name }}</h4>
    </div>
    <div class="card-body chat-content">

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

    </div>

    <div class="card-footer chat-form">
        <form id="chat_form">
            @csrf
            <input type="text" class="form-control contact_message" placeholder="Type a message" name="message" autocomplete="off">
            <input type="hidden" name="receiver_id" value="{{ $contact_author->id }}">
            <button type="submit" class="btn btn-primary">
                <i class="far fa-paper-plane"></i>
            </button>
        </form>
    </div>


    <script>
        "use strict";
        (function($) {
            $("#chat_form").on("submit", function(event){
                event.preventDefault()

                if($('.contact_message').val()){
                    $.ajax({
                        type : 'post',
                        data : $(this).serialize(),
                        url : "{{ route('send-message') }}",
                        success : function(response){
                            $('.contact_message').val('');
                            $(".chat-content").html(response);
                            scrollToBottomFunc();
                        },
                        error : function(error){
                            alert("server error")
                        }
                    })
                }


            })

        })(jQuery);
    </script>

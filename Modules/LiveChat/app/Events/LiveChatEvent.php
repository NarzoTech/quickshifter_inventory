<?php

namespace Modules\LiveChat\app\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class LiveChatEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $event_message;

    public function __construct($event_message)
    {
        $this->event_message = $event_message;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn()
    {
        return new PrivateChannel('livechat.'.$this->event_message->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'message_id' => $this->event_message->id,
            'sender_id' => $this->event_message->sender_id,
        ];
    }

    public function broadcastAs()
    {
        return 'live-chat';
    }
}

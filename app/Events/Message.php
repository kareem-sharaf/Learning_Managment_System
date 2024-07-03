<?php

namespace App\Events;

use App\Models\Message as MessageModel;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Message implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(MessageModel $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('messages.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->message,
            'sender_name' => $this->message->sender->name,
        ];
    }
}

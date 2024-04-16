<?php

namespace App\Listeners;

use App\Events\Message;
use App\Models\MessageModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SaveMessageNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Message  $event
     * @return void
     */
    public function handle(Message $event)
    {
        $messageModel = new MessageModel();
        $messageModel->username = $event->username;
        $messageModel->message = $event->message;
        $messageModel->save();
    }
}
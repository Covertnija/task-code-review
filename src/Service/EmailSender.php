<?php

namespace App\Service;

use App\Model\Message;

class EmailSender implements SenderInterface
{
    public function supports(Message $message)
    {
        return $message->type == Message::TYPE_EMAIL;
    }

    public function send(Message $message)
    {
        print "Email";
    }
}

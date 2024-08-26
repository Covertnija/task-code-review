<?php

namespace App\Service;

use App\Model\Message;

class SMSSender implements SenderInterface
{
    public function supports(Message $message): bool
    {
        return $message->type == Message::TYPE_SMS;
    }

    public function send(Message $message)
    {
        print "SMS";
    }
}

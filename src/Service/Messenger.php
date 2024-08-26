<?php

namespace App\Service;


use App\Model\Message;

class Messenger
{
    /**
     * @var SenderInterface[]
     */
    protected array $senders = [];

    /**
     * Messenger constructor.
     * @param SenderInterface[] $senders
     */
    public function __construct(array $senders = [])
    {
        $this->senders = $senders;
    }

    public function send(Message $message):void
    {
        foreach ($this->senders as $sender) {
            if ($sender->supports($message)) {
                $sender->send($message);
            }
        }
    }
}

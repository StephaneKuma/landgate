<?php

namespace App\Repositories;

use App\Models\Message;


class MessageRepository extends ResourceRepository
{
    public function __construct(Message $message)
    {
        parent::__construct($message);
    }
}
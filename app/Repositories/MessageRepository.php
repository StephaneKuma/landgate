<?php

namespace App\Repositories;

use App\Models\Message;


class MessageRepository extends ResourceRepository
{
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
        parent::__construct($this->message);
    }

    public function getLatest(int $agent_id, int $nbr_per_page)
    {
        return $this->message
            ->where('agent_id', $agent_id)
            ->paginate($nbr_per_page);
    }

    public function countByUserId(int $agent_id)
    {
        return $this->message
            ->where('agent_id', $agent_id)
            ->count();
    }
}
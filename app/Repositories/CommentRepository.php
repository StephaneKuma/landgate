<?php

namespace App\Repositories;

use App\Models\Comment;


class CommentRepository extends ResourceRepository
{
    private $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
        parent::__construct($this->comment);
    }

    public function getLatest(int $user_id, int $nbr_per_page)
    {
        return $this->comment
            ->with('commentable')
            ->where('user_id', $user_id)
            ->paginate($nbr_per_page);
    }

    public function countByUserId(int $user_id)
    {
        return $this->comment
            ->where('user_id', $user_id)
            ->count();
    }
}
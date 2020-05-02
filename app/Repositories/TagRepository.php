<?php

namespace App\Repositories;

use App\Models\Tag;


class TagRepository extends ResourceRepository
{
    public function __construct(Tag $tag)
    {
        parent::__construct($tag);
    }
}
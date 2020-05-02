<?php

namespace App\Repositories;

use App\Models\Rating;


class RatingRepository extends ResourceRepository
{
    public function __construct(Rating $rating)
    {
        parent::__construct($rating);
    }
}
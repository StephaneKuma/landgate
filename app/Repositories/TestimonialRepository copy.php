<?php

namespace App\Repositories;

use App\Models\Testimonial;


class TestimonialRepository extends ResourceRepository
{
    public function __construct(Testimonial $testimonial)
    {
        parent::__construct($testimonial);
    }
}
<?php

namespace App\Repositories;

use App\Models\Slider;


class SliderRepository extends ResourceRepository
{
    public function __construct(Slider $slider)
    {
        parent::__construct($slider);
    }
}
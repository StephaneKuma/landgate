<?php

namespace App\Repositories;

use App\Models\Feature;


class FeatureRepository extends ResourceRepository
{
    public function __construct(Feature $feature)
    {
        parent::__construct($feature);
    }
}
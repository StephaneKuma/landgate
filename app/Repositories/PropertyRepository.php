<?php

namespace App\Repositories;

use App\Models\Property;


class PropertyRepository extends ResourceRepository
{
    public function __construct(Property $property)
    {
        parent::__construct($property);
    }
}
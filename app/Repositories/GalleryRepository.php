<?php

namespace App\Repositories;

use App\Models\Gallery;


class GalleryRepository extends ResourceRepository
{
    public function __construct(Gallery $gallery)
    {
        parent::__construct($gallery);
    }
}
<?php

namespace App\Repositories;

use App\Models\Album;


class AlbumRepository extends ResourceRepository
{
    public function __construct(Album $album)
    {
        parent::__construct($album);
    }
}
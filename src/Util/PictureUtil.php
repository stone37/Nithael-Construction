<?php

namespace App\Util;

use App\Service\PictureService;

class PictureUtil
{
    public function __construct(private readonly PictureService $service)
    {
    }

    public function getPath(Object $picture, string $filter): string
    {
        return $this->service->getPicturePath($picture, $filter, 'file');
    }
}
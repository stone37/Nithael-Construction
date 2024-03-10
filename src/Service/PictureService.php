<?php

namespace App\Service;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PictureService
{
    public function __construct(
        private readonly UploaderHelper $uploaderHelper,
        private readonly CacheManager $imagineCacheManager
    )
    {
    }

    public function getPicturePath(object $image, string $filter, string $fileField): string
    {
        $picturePath = $this->uploaderHelper->asset($image, $fileField);

        return $this->imagineCacheManager->getBrowserPath($picturePath, $filter);
    }
}
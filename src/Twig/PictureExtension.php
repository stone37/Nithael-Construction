<?php

namespace App\Twig;

use App\Util\PictureUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PictureExtension extends AbstractExtension
{
    public function __construct(private readonly PictureUtil $util)
    {
    }

    public function getFilters(): array
    {
        return [new TwigFilter('image_url', [$this->util, 'getPath'])];
    }
}

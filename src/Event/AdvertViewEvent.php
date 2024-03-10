<?php

namespace App\Event;

use App\Entity\Advert;

class AdvertViewEvent
{
    public function __construct(private readonly Advert $advert)
    {
    }

    public function getAdvert(): Advert
    {
        return $this->advert;
    }
}
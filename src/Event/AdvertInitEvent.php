<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\Request;

class AdvertInitEvent
{
    public function __construct(private readonly Request $request)
    {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
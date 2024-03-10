<?php

namespace App\Data;

use App\Form\AdvertType;

class AdvertCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return AdvertType::class;
    }
}


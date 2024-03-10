<?php

namespace App\Data;

use App\Form\EmailingType;

class EmailingCrudData extends AutomaticCrudData
{
    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return EmailingType::class;
    }
}

<?php

namespace App\Data;

use App\Form\SettingsModuleType;

class SettingsModuleCrudData extends AutomaticCrudData
{
    public ?bool $activeReference = null;

    public ?bool $activeBlog = null;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return SettingsModuleType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setActiveReference($this->activeReference)
            ->setActiveBlog($this->activeBlog);
    }
}
<?php

namespace App\Data;

use App\Form\SettingsModuleType;

class SettingsModuleCrudData extends AutomaticCrudData
{
    public ?bool $activeReview = null;

    public ?bool $activeBlog = null;

    public ?bool $activeAchieve = null;

    public ?bool $activeTeam = null;

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
            ->setActiveReview($this->activeReview)
            ->setActiveBlog($this->activeBlog)
            ->setActiveAchieve($this->activeAchieve)
            ->setActiveTeam($this->activeTeam);
    }
}
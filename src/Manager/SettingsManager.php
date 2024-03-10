<?php

namespace App\Manager;

use App\Entity\Settings;
use App\Repository\SettingsRepository;

class SettingsManager
{
    public function __construct(private readonly SettingsRepository $repository)
    {
    }

    public function get(): ?Settings
    {
        return $this->repository->getSettings();
    }
}


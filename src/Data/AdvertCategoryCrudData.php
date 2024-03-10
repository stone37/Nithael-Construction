<?php

namespace App\Data;

use App\Form\AdvertCategoryType;
use Symfony\Component\Validator\Constraints as Assert;

class AdvertCategoryCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $name = null;

    public ?bool $enabled = true;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return AdvertCategoryType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setEnabled($this->enabled)
        ;
    }
}
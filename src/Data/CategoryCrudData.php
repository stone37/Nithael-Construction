<?php

namespace App\Data;

use App\Form\CategoryType;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryCrudData extends AutomaticCrudData
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
        return CategoryType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setEnabled($this->enabled)
        ;
    }
}
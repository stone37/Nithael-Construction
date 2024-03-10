<?php

namespace App\Data;

use App\Form\ServiceType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $description = null;

    public ?bool $enabled = true;

    public ?UploadedFile $file = null;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return ServiceType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setDescription($this->description)
            ->setEnabled($this->enabled)
            ->setFile($this->file);
    }
}
<?php

namespace App\Data;

use App\Form\ReferenceType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class ReferenceCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public ?string $name = null;

    public ?bool $enabled = true;

    public ?UploadedFile $file = null;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return ReferenceType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setName($this->name)
            ->setEnabled($this->enabled)
            ->setFile($this->file)
        ;
    }
}
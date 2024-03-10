<?php

namespace App\Data;

use App\Form\CarouselItemType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CarouselItemCrudData extends AutomaticCrudData
{
    public ?string $title = null;

    public ?string $description = null;

    public ?string $url = null;

    public ?bool $enabled = true;

    #[Assert\NotBlank]
    public ?UploadedFile $file = null;

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return CarouselItemType::class;
    }

    public function hydrate(): void
    {
        $this->entity
            ->setTitle($this->title)
            ->setDescription($this->description)
            ->setUrl($this->url)
            ->setEnabled($this->enabled)
            ->setFile($this->file);
    }
}
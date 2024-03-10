<?php

namespace App\Data;

use App\Entity\Advert;
use App\Form\CarouselItemType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CarouselItemCrudData extends AutomaticCrudData
{
    public ?string $title = null;

    #[Assert\NotBlank]
    public ?Advert $advert = null;

    public ?bool $enabled = true;

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
            ->setAdvert($this->advert)
            ->setEnabled($this->enabled)
            ->setFile($this->file);
    }
}
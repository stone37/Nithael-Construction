<?php

namespace App\Model\Admin;

use App\Entity\AdvertCategory;

class AdvertSearch
{
    private ?AdvertCategory $category = null;

    private ?string $type = null;

    private ?bool $enabled = null;

    private ?string $city = null;

    public function getCategory(): ?AdvertCategory
    {
        return $this->category;
    }

    public function setCategory(?AdvertCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }
}

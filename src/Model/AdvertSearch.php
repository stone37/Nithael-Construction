<?php

namespace App\Model;

class AdvertSearch
{
    private ?string $category = '';

    private ?string $type = '';

    private ?string $city = '';

    private ?array $price = [];

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPrice(): ?array
    {
        return $this->price;
    }

    public function setPrice(?array $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'category' => $this->getCategory(),
            'type' => $this->getType(),
            'city' => $this->getCity(),
            'price' => $this->getPrice()
        ];
    }
}

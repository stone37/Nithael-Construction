<?php

namespace App\Data;

use Symfony\Component\Validator\Constraints as Assert;

class AdvertMessageData
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public ?string $firstname = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    public ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $phone = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    public ?string $content = null;
}
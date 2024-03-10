<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Entrez un prénom s\'il vous plait.')]
    #[Assert\Length(min: 2, max: 180, minMessage: 'Le prénom est trop court.', maxMessage: 'Le prénom est trop long.')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: 'Entrez une adresse e-mail s\'il vous plait.')]
    #[Assert\Length(min: 2, max: 180, minMessage: 'L\'adresse e-mail est trop courte.', maxMessage: 'L\'adresse e-mail est trop longue.')]
    #[Assert\Email(message: 'L\'adresse e-mail est invalide.')]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Entrez un numéro de téléphone s\'il vous plait.')]
    #[Assert\Length(min: 8, max: 180, minMessage: 'Le numéro de téléphone est trop court.', maxMessage: 'Le numéro de téléphone est trop long.')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[Assert\NotBlank(message: 'Veuillez renseigne votre message.')]
    #[Assert\Length(min: 10, max: 180, minMessage: 'Le message est trop court.', maxMessage: 'Le message est trop long.')]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Advert $advert = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }
}

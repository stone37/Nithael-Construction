<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\AdvertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: AdvertRepository::class)]
class Advert
{
    use EnabledTrait;
    use PositionTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 120)]
    #[ORM\Column(length: 120, nullable: true)]
    private ?string $title = null;

    #[Gedmo\Slug(fields: ['title'], unique: true)]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $slug = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\ManyToOne]
    private ?Category $category = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $subCategory = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(nullable: true)]
    private ?int $surface = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombrePiece = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreChambre = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombreSalleBain = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dateConstruction = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $standing = null;

    #[ORM\Column]
    private ?bool $isPayment = false;

    #[ORM\OneToMany(mappedBy: 'adverts', targetEntity: AdvertRead::class, orphanRemoval: true)]
    private Collection $reads;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'adverts', targetEntity: Gallery::class, orphanRemoval: true)]
    private Collection $pictures;

    public function __construct()
    {
        $this->reads = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSubCategory(): ?Category
    {
        return $this->subCategory;
    }

    public function setSubCategory(?Category $subCategory): self
    {
        $this->subCategory = $subCategory;

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

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(?string $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(?int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getNombrePiece(): ?int
    {
        return $this->nombrePiece;
    }

    public function setNombrePiece(?int $nombrePiece): self
    {
        $this->nombrePiece = $nombrePiece;

        return $this;
    }

    public function getNombreChambre(): ?int
    {
        return $this->nombreChambre;
    }

    public function setNombreChambre(?int $nombreChambre): self
    {
        $this->nombreChambre = $nombreChambre;

        return $this;
    }

    public function getNombreSalleBain(): ?int
    {
        return $this->nombreSalleBain;
    }

    public function setNombreSalleBain(?int $nombreSalleBain): self
    {
        $this->nombreSalleBain = $nombreSalleBain;

        return $this;
    }

    public function getDateConstruction(): ?string
    {
        return $this->dateConstruction;
    }

    public function setDateConstruction(?string $dateConstruction): self
    {
        $this->dateConstruction = $dateConstruction;

        return $this;
    }

    public function getStanding(): ?string
    {
        return $this->standing;
    }

    public function setStanding(?string $standing): self
    {
        $this->standing = $standing;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->title;
    }

    public function isIsPayment(): ?bool
    {
        return $this->isPayment;
    }

    public function setIsPayment(bool $isPayment): self
    {
        $this->isPayment = $isPayment;

        return $this;
    }

    /**
     * @return Collection<int, AdvertRead>
     */
    public function getReads(): Collection
    {
        return $this->reads;
    }

    public function addRead(AdvertRead $read): self
    {
        if (!$this->reads->contains($read)) {
            $this->reads[] = $read;
            $read->setAdverts($this);
        }

        return $this;
    }

    public function removeRead(AdvertRead $read): self
    {
        if ($this->reads->removeElement($read)) {
            // set the owning side to null (unless already changed)
            if ($read->getAdverts() === $this) {
                $read->setAdverts(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setAdvert($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAdvert() === $this) {
                $message->setAdvert(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Gallery>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Gallery $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setAdverts($this);
        }

        return $this;
    }

    public function removePicture(Gallery $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAdverts() === $this) {
                $picture->setAdverts(null);
            }
        }

        return $this;
    }
}

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
    const TYPE_OFFER = 'Vente';
    const TYPE_LOCATION = 'Location';

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

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $type = self::TYPE_OFFER;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[Assert\NotBlank]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?AdvertCategory $category = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $district = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberOfViews = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'VILLA', 'STUDIO', 'TERRAIN', 'ESPACE_COMMERCIAUX'])]
    #[ORM\Column(nullable: true)]
    private ?int $surface = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'VILLA'])]
    #[ORM\Column(nullable: true)]
    private ?int $nombrePiece = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'VILLA'])]
    #[ORM\Column(nullable: true)]
    private ?int $nombreChambre = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'VILLA'])]
    #[ORM\Column(nullable: true)]
    private ?int $nombreSalleBain = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dateConstruction = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $standing = null;

    #[ORM\OneToMany(targetEntity: AdvertPicture::class, mappedBy: 'advert', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $pictures;

    public function __construct()
    {
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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

    public function getCategory(): ?AdvertCategory
    {
        return $this->category;
    }

    public function setCategory(?AdvertCategory $category): self
    {
        $this->category = $category;

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

    public function getNumberOfViews(): ?int
    {
        return $this->numberOfViews;
    }

    public function setNumberOfViews(?int $numberOfViews): self
    {
        $this->numberOfViews = $numberOfViews;

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
     * @return Collection<int, AdvertPicture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(AdvertPicture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setAdvert($this);
        }

        return $this;
    }

    public function removePicture(AdvertPicture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAdvert() === $this) {
                $picture->setAdvert(null);
            }
        }

        return $this;
    }
}

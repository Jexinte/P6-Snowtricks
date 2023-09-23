<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $trickGroup = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: true)]
    private ?bool $trickUpdated = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private Collection $media;

    private mixed $mediaForm;

    public function __construct()
    {
        $this->media = new ArrayCollection();
    }

    private string $nameUpdated;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTrickGroup(): ?string
    {
        return $this->trickGroup;
    }

    public function setTrickGroup(string $trickGroup): static
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTrickUpdated(): ?bool
    {
        return $this->trickUpdated;
    }

    public function isTrickUpdated(?bool $trickUpdated): static
    {
        $this->trickUpdated = $trickUpdated;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media $media): static
    {
        if (!$this->media->contains($media)) {
            $this->media->add($media);
            $media->setTrick($this);
        }

        return $this;
    }


    /**
     * @return mixed
     */
    public function getMediaForm()
    {
        return $this->mediaForm;
    }

    /**
     * @param mixed $mediaForm
     */
    public function setMediaForm($mediaForm): void
    {
        $this->mediaForm = $mediaForm;
    }

    /**
     * @return string
     */
    public function getNameUpdated(): string
    {
        return $this->nameUpdated;
    }

    /**
     * @param string $nameUpdated
     */
    public function setNameUpdated(string $nameUpdated): void
    {
        $this->nameUpdated = $nameUpdated;
    }
}

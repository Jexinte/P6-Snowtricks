<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $trickGroup = null;


    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $trickUpdated;

    private string $nameUpdated;

    private ?bool $nameAlreadyExist;

    /**
     * @var array<string>
     */
    private array $images;
    /**
     * @var array<string>
     */
    private array $videos;
    private ?string $embedUrl = null;
    private ?UploadedFile $bannerFile;

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

    public function setTrickGroup(?string $trickGroup): static
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }


    /**
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setDate(?\DateTimeInterface $date): void
    {
        $this->date = $date;
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


    public function getNameAlreadyExist(): ?bool
    {
        return $this->nameAlreadyExist;
    }


    public function isNameAlreadyExist(?bool $nameAlreadyExist): void
    {
        $this->nameAlreadyExist = $nameAlreadyExist;
    }


    /**
     * @return UploadedFile|null
     */
    public function getBannerFile(): ?UploadedFile
    {
        return $this->bannerFile;
    }

    /**
     * @param UploadedFile|null $bannerFile
     */
    public function setBannerFile(?UploadedFile $bannerFile): void
    {
        $this->bannerFile = $bannerFile;
    }

    /**
     * @return array<string>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array<string> $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    /**
     * @return array<string>
     */
    public function getVideos(): array
    {
        return $this->videos;
    }

    /**
     * @param array<string> $videos
     */
    public function setVideos(array $videos): void
    {
        $this->videos = $videos;
    }

    /**
     * @return string|null
     */
    public function getEmbedUrl(): ?string
    {
        return $this->embedUrl;
    }

    /**
     * @param string|null $embedUrl
     */
    public function setEmbedUrl(?string $embedUrl): void
    {
        $this->embedUrl = $embedUrl;
    }

    /**
     * @return bool|null
     */
    public function getTrickUpdated(): ?bool
    {
        return $this->trickUpdated;
    }

    /**
     * @param bool|null $trickUpdated
     */
    public function isTrickUpdated(?bool $trickUpdated): void
    {
        $this->trickUpdated = $trickUpdated;
    }

}

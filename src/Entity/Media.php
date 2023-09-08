<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idTrick = null;

    #[ORM\Column(length: 255)]
    private ?string $mediaPath = null;

    #[ORM\Column(length: 255)]
    private ?string $mediaType = null;


    /**
     *
     * @var array<string>
     */
    private array $images;

    /**
     * @var array<string>
     */
    private array $videos;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdTrick(): ?int
    {
        return $this->idTrick;
    }

    /**
     * @param int $idTrick
     * @return $this
     */
    public function setIdTrick(int $idTrick): static
    {
        $this->idTrick = $idTrick;

        return $this;
    }

    public function getMediaPath(): ?string
    {
        return $this->mediaPath;
    }

    /**
     * @param string $mediaPath
     * @return $this
     */
    public function setMediaPath(string $mediaPath): static
    {
        $this->mediaPath = $mediaPath;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    /**
     * @param string $mediaType
     * @return $this
     */
    public function setMediaType(string $mediaType): static
    {
        $this->mediaType = $mediaType;

        return $this;
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
     * @return $this
     */
    public function setImages(array $images): static
    {
        $this->images = $images;
        return $this;
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
     * @return void
     */
    public function setVideos(array $videos): void
    {
        $this->videos = $videos;
    }


}

<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    #[ORM\Column(length: 255,nullable: true)]
    private ?bool $isBanner = null;


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


    private ?UploadedFile $updatedFile = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(name:'id_trick',referencedColumnName: 'id',nullable: false)]
    private ?Trick $trick = null;

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

    /**
     * @return bool|null
     */
    public function getIsBanner(): ?bool
    {
        return $this->isBanner;
    }

    /**
     * @param bool|null $isBanner
     */
    public function setIsBanner(?bool $isBanner = null): void
    {
        $this->isBanner = $isBanner;
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
//
//    /**
//     * @return UploadedFile|null
//     */
    public function getBannerFile()
    {
        return $this->bannerFile;
    }

//    /**
//     * @param UploadedFile|null $bannerFile
//     */
    public function setBannerFile( $bannerFile): void
    {
        $this->bannerFile = $bannerFile;
    }



    /**
     * @return UploadedFile|null
     */
    public function getUpdatedFile(): ?UploadedFile
    {
        return $this->updatedFile;
    }

    /**
     * @param UploadedFile|null $updatedFile
     */
    public function setUpdatedFile(?UploadedFile $updatedFile): void
    {
        $this->updatedFile = $updatedFile;
    }


    /**
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }


    /**
     * @param array<string> $images
     * @return void
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): static
    {
        $this->trick = $trick;

        return $this;
    }


}

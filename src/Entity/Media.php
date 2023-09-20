<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\All([
        new Assert\File(
            maxSize: '3000K',
            groups: ['illustration_exception'],
            extensions: ['jpg', 'png', 'webp'],
            extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
        ),

    ])]

    private array $illustrations;
    /**
     * @var array<string>
     */
    #[Assert\All([
        new Assert\File(
            maxSize: '3000K',
            groups: ['video_exception'],
            extensions: ['mp4'],
            extensionsMessage: 'Seuls les fichiers mp4 sont acceptés !'
        ),
    ])]

    private array $videos;


    #[Assert\Regex(
        pattern: '/<iframe[^>]+src="([^"]+)"/i',
        message: "Oops ! Il semblerait que le format de votre url n'est pas bon, merci de vérifier ce qu'il en est",
        match: true,
        groups: ['url_exception']
    )]
    private ?string $embedUrl = null;

    #[Assert\File(
        maxSize: '3000K',
        groups: ['banner_exception'],
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    #[Assert\NotBlank(
        message: 'Veuillez sélectionner un fichier !',
        groups: ['banner_exception']
    )]
    private ?UploadedFile $bannerFile;

    #[Assert\File(
        maxSize: '3000K',
        groups: ['update_file_exception'],
        extensions: ['jpg', 'png', 'webp','mp4'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg,png,webp et mp4 sont acceptés !'
    )]
    private ?UploadedFile $updatedFile = null;

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
    public function getIllustrations(): array
    {
        return $this->illustrations;
    }

    /**
     * @param array<string> $illustrations
     */
    public function setIllustrations(array $illustrations): void
    {
        $this->illustrations = $illustrations;
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


}

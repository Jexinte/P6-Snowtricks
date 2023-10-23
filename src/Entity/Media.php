<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

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
    #[ORM\Column(length: 255, nullable: true)]
    private ?bool $isBanner = null;


    /**
     * @var array<string>
     */

    #[Assert\All(
        new File(
            maxSize: '3000K',
            extensions: ['jpg', 'png', 'webp'],
            extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !',
        )
    )]
    private array $images;
    /**
     * @var array<string>
     */


    #[Assert\All(
        new File(
            maxSize: '3000K',
            extensions: ['mp4'],
            extensionsMessage: 'Seuls les fichiers ayant pour extension mp4 sont acceptés !',
        )
    )]
    private array $videos;

    #[Assert\Regex(
        pattern: '/<iframe[^>]+src="([^"]+)"/i',
        message: "Oops ! Il semblerait que le format de votre url n'est pas bon, merci de vérifier ce qu'il en est",
        match: true,
    )]
    private ?string $embedUrl = null;
    #[Assert\Regex(
        pattern: '/<iframe[^>]+src="([^"]+)"/i',
        message: "Oops ! Il semblerait que le format de votre url n'est pas bon, merci de vérifier ce qu'il en est",
        match: true,
        groups: ['updateTrickUrl']
    )]
    private ?string $embedUrlUpdated = null;


    #[Assert\NotBlank(message:'Veuillez sélectionner un fichier !')]
    #[Assert\File(
        maxSize: '3000K',
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    private ?UploadedFile $bannerFile;


    #[Assert\File(
        maxSize: '3000K',
        groups: ['updateTrickFileThatIsNotBanner'],
        extensions: ['jpg', 'png', 'webp','mp4'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    private ?UploadedFile $updatedFile = null;
    #[Assert\File(
        maxSize: '3000K',
        groups: ['updateBannerFile'],
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    private ?UploadedFile $updatedBannerFile = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(name:'id_trick', referencedColumnName: 'id', nullable: false)]
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

    public function getBannerFile(): UploadedFile
    {
        return $this->bannerFile;
    }


    public function setBannerFile(UploadedFile $bannerFile): void
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

    /**
     * @return UploadedFile|null
     */
    public function getUpdatedBannerFile(): ?UploadedFile
    {
        return $this->updatedBannerFile;
    }

    /**
     * @param UploadedFile|null $updatedBannerFile
     */
    public function setUpdatedBannerFile(?UploadedFile $updatedBannerFile): void
    {
        $this->updatedBannerFile = $updatedBannerFile;
    }

    /**
     * @return string|null
     */
    public function getEmbedUrlUpdated(): ?string
    {
        return $this->embedUrlUpdated;
    }

    /**
     * @param string|null $embedUrlUpdated
     */
    public function setEmbedUrlUpdated(?string $embedUrlUpdated): void
    {
        $this->embedUrlUpdated = $embedUrlUpdated;
    }


}

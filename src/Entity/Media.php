<?php
/**
 * Handle media properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  Media
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;

/**
 * Handle media properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  Media
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
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


    #[Assert\All(
        new File(
            maxSize: '3000K',
            extensions: ['jpg', 'png', 'webp'],
            extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !',
        )
    )]
    private array $images;


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


    #[Assert\NotBlank(message: 'Veuillez sélectionner un fichier !')]
    #[Assert\File(
        maxSize: '3000K',
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    private ?UploadedFile $bannerFile;


    #[Assert\File(
        maxSize: '3000K',
        groups: ['updateTrickFileThatIsNotBanner'],
        extensions: ['jpg', 'png', 'webp', 'mp4'],
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
    #[ORM\JoinColumn(name: 'id_trick', referencedColumnName: 'id', nullable: false)]
    private ?Trick $trick = null;

    /**
     * Summary of getId
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Summary of getIdTrick
     *
     * @return int|null
     */
    public function getIdTrick(): ?int
    {
        return $this->idTrick;
    }

    /**
     * Summary of setIdTrick
     *
     * @param int $idTrick int
     *
     * @return $this
     */
    public function setIdTrick(int $idTrick): static
    {
        $this->idTrick = $idTrick;

        return $this;
    }

    /**
     * Summary of getMediaPath
     *
     * @return string|null
     */
    public function getMediaPath(): ?string
    {
        return $this->mediaPath;
    }

    /**
     * Summary of setMediaPath
     *
     * @param string $mediaPath string
     *
     * @return $this
     */
    public function setMediaPath(string $mediaPath): static
    {
        $this->mediaPath = $mediaPath;

        return $this;
    }

    /**
     * Summary of getMediaType
     *
     * @return string|null
     */
    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    /**
     * Summary of setMediaType
     *
     * @param string $mediaType string
     *
     * @return $this
     */
    public function setMediaType(string $mediaType): static
    {
        $this->mediaType = $mediaType;

        return $this;
    }


    /**
     * Summary of getVideos
     *
     * @return array<string>
     */
    public function getVideos(): array
    {
        return $this->videos;
    }


    /**
     * Summary of getVideos
     *
     * @param array<string> $videos array
     *
     * @return void
     */
    public function setVideos(array $videos): void
    {
        $this->videos = $videos;
    }

    /**
     * Summary of getIsBanner
     *
     * @return bool|null
     */
    public function getIsBanner(): ?bool
    {
        return $this->isBanner;
    }

    /**
     * Summary of setIsBanner
     *
     * @param bool|null $isBanner ?bool
     *
     * @return void
     */
    public function setIsBanner(?bool $isBanner = null): void
    {
        $this->isBanner = $isBanner;
    }

    /**
     * Summary of getEmbedUrl
     *
     * @return string|null
     */
    public function getEmbedUrl(): ?string
    {
        return $this->embedUrl;
    }

    /**
     * Summary of setEmbedUrl
     *
     * @param string|null $embedUrl ?string
     *
     * @return void
     */
    public function setEmbedUrl(?string $embedUrl): void
    {
        $this->embedUrl = $embedUrl;
    }

    /**
     * Summary of getBannerFile
     *
     * @return UploadedFile
     */
    public function getBannerFile(): UploadedFile
    {
        return $this->bannerFile;
    }


    /**
     * Summary of setBannerFile
     *
     * @param UploadedFile $bannerFile Object
     *
     * @return void
     */
    public function setBannerFile(UploadedFile $bannerFile): void
    {
        $this->bannerFile = $bannerFile;
    }


    /**
     * Summary of getUpdatedFile
     *
     * @return UploadedFile|null
     */
    public function getUpdatedFile(): ?UploadedFile
    {
        return $this->updatedFile;
    }

    /**
     * Summary of setUpdatedFile
     *
     * @param UploadedFile|null $updatedFile Object
     * 
     * @return void
     */
    public function setUpdatedFile(?UploadedFile $updatedFile): void
    {
        $this->updatedFile = $updatedFile;
    }


    /**
     * Summary of getImages
     *
     * @return string[]
     */
    public function getImages(): array
    {
        return $this->images;
    }


    /**
     * Summary of setImages
     *
     * @param array<string> $images array
     *
     * @return void
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }

    /**
     * Summary of getTrick
     *
     * @return Trick|null
     */
    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    /**
     * Summary of setTrick
     *
     * @param Trick|null $trick Object
     *
     * @return $this
     */
    public function setTrick(?Trick $trick): static
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * Summary of getUpdatedBannerFile
     *
     * @return UploadedFile|null
     */
    public function getUpdatedBannerFile(): ?UploadedFile
    {
        return $this->updatedBannerFile;
    }

    /**
     * Summary of setUpdatedBannerFile
     *
     * @param UploadedFile|null $updatedBannerFile Object
     *
     * @return void
     */
    public function setUpdatedBannerFile(?UploadedFile $updatedBannerFile): void
    {
        $this->updatedBannerFile = $updatedBannerFile;
    }

    /**
     * Summary of getEmbedUrlUpdated
     *
     * @return string|null
     */
    public function getEmbedUrlUpdated(): ?string
    {
        return $this->embedUrlUpdated;
    }

    /**
     * Summary of setEmbedUrlUpdated
     *
     * @param string|null $embedUrlUpdated Object
     *
     * @return void
     */
    public function setEmbedUrlUpdated(?string $embedUrlUpdated): void
    {
        $this->embedUrlUpdated = $embedUrlUpdated;
    }


}

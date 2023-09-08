<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity(
    fields: 'name',
    message: "Le nom de votre trick existe déjà, merci d'en choisir un autre !",
    groups: ["name_exception"]
)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'name_exception',
        ]
    )]
    #[Assert\Regex(
        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
        message: 'Oops! Le format de votre saisie est incorrect, le nom du trick doit commencer par une lettre majuscule',
        match: true,
        groups: ['name_exception']
    )]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'description_exception',
        ]
    )]
    #[Assert\Regex(
        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
        message: 'Oops! Le format de votre saisie est incorrect, votre description doit commencer par une lettre majuscule',
        match: true,
        groups: ['description_exception']
    )]
    #[ORM\Column(length: 255)]
    private ?string $description = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'group_exception',
        ]
    )]
    #[ORM\Column(length: 255)]
    private ?string $trickGroup = null;


    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;
    /**
     * @var array<string>
     */
    #[Assert\All([
        new Assert\File(
            maxSize: '3000K',
            groups: ['illustration_exception'],
            extensions: ['jpg', 'png', 'webp'],
            extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
        )

    ])]
//    #[Assert\NotBlank(
//        message: 'Veuillez sélectionner un fichier !',
//        groups: ['illustration_exception']
//    )]
    private array $images;
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
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $embedUrl;

    #[ORM\Column(length: 255)]
    private ?string $mainBannerFilePath;
    #[Assert\NotBlank(
        message: 'Veuillez sélectionner un fichier !',
        groups: ['main_banner_exception']
    )]
    #[Assert\File(
        maxSize: '3000K',
        groups: ['main_banner_exception'],
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]

    private ?UploadedFile $mainBannerFile;

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
     * @return string[]|null
     */
    public function getImages(): ?array
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


    /**
     * @return string[]|null
     */
    public function getVideos(): ?array
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
     * @return ?string
     */
    public function getEmbedUrl(): ?string
    {
        return $this->embedUrl;
    }

    /**
     * @param ?string $youtubeUrl
     */
    public function setEmbedUrl(?string $youtubeUrl): void
    {
        $this->embedUrl = $youtubeUrl;
    }

    /**
     * @return string|null
     */
    public function getMainBannerFilePath(): ?string
    {
        return $this->mainBannerFilePath;
    }


    /**
     * @param string|null $mainBannerFilePath
     * @return void
     */
    public function setMainBannerFilePath(?string $mainBannerFilePath): void
    {
        $this->mainBannerFilePath = $mainBannerFilePath;
    }


    /**
     * @return UploadedFile
     */
    public function getMainBannerFile(): UploadedFile
    {
        return $this->mainBannerFile;
    }


    /**
     * @param UploadedFile $mainBannerFile
     * @return $this
     */
    public function setMainBannerFile(UploadedFile $mainBannerFile): static
    {
        $this->mainBannerFile = $mainBannerFile;
        return  $this;
    }
}

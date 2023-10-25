<?php
/**
 * Handle trick properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  Trick
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity(fields: 'name', message: 'Désolé, le trick que vous avez demandé n\'est actuellement pas disponible, veuillez en définir un autre !')]
#[AllowDynamicProperties]

/**
 * Handle trick properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  Trick
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champ ne peut être vide !')]
    #[Assert\Regex(
        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
        message: 'Oops! Le format de votre saisie est incorrect, le nom du trick doit commencer par une lettre majuscule',
        match: true,
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champ ne peut être vide !')]
    #[Assert\Regex(
        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
        message: 'Oops! Le format de votre saisie est incorrect, votre description doit commencer par une lettre majuscule',
        match: true,
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champ ne peut être vide !')]
    private ?string $trickGroup = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;




    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Media::class, cascade: ['persist', 'remove'])]
    private Collection $media;

    #[Assert\Type(type:Media::class)]
    #[Assert\Valid]
    private mixed $mediaForm;
    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class, cascade: ['persist', 'remove'])]
    private Collection $comment;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->media = new ArrayCollection();
        $this->comment = new ArrayCollection();

    }

    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields:['name'])]
    private string $slug;

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
     * Summary of getName
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Summary of setName
     *
     * @param string $name string
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Summary of getDescription
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Summary of setDescription
     *
     * @param string $description string
     *
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Summary of getTrickGroup
     *
     * @return string|null
     */
    public function getTrickGroup(): ?string
    {
        return $this->trickGroup;
    }

    /**
     * Summary of setTrickGroup
     *
     * @param string $trickGroup string
     *
     * @return $this
     */
    public function setTrickGroup(string $trickGroup): static
    {
        $this->trickGroup = $trickGroup;

        return $this;
    }



    /**
     * Summary of getMedia
     *
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    /**
     * Summary of addMedia
     *
     * @param Media $media Object
     *
     * @return $this
     */
    public function addMedia(Media $media): static
    {
        if (!$this->media->contains($media)) {
            $this->media->add($media);
            $media->setTrick($this);
        }

        return $this;
    }

    /**
     * Summary of getComment
     *
     * @return Collection
     */
    public function getComment(): Collection
    {
        return $this->comment;
    }

    /**
     * Summary of addComment
     *
     * @param Comment $comment Object
     *
     * @return $this
     */
    public function addComment(Comment $comment): static
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }


    /**
     * Summary of getMediaForm
     *
     * @return mixed
     */
    public function getMediaForm()
    {
        return $this->mediaForm;
    }

    /**
     * Summary of setMediaForm
     *
     * @param mixed $mediaForm mixed
     *
     * @return void
     */
    public function setMediaForm($mediaForm): void
    {
        $this->mediaForm = $mediaForm;
    }



    /**
     * Summary of getCreatedAt
     *
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * Summary of setCreatedAt
     *
     * @param \DateTimeInterface|null $created_at Object
     *
     * @return void
     */
    public function setCreatedAt(?\DateTimeInterface $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * Summary of getUpdatedAt
     *
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * Summary of setUpdatedAt
     *
     * @param \DateTimeInterface|null $updated_at Object
     *
     * @return void
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Summary of getSlug
     *
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }




}

<?php
/**
 * Handle comment properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  Comment
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Entity;

use AllowDynamicProperties;
use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[AllowDynamicProperties]

/**
 * Handle comment properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  Comment
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $idUser = null;
    #[ORM\Column(nullable: true)]
    private ?int $idTrick = null;

    #[ORM\Column(length: 255)]
    private ?string $userProfileImage = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $createdAt = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne peut être vide !')]
    #[Assert\Regex(
        pattern: '/^[A-ZÀ-ÿ][A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]{0,498}[A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]$/',
        message: 'Un commentaire 
    doit commencer par une lettre majuscule
     et ne peut excéder 500 caractères',
        match: true,
    )]
    private ?string $content = null;

    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(name: 'id_trick', referencedColumnName: 'id', nullable: false)]
    private ?Trick $trick = null;
    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

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
     * Summary of getIdUser
     *
     * @return int|null
     */
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    /**
     * Summary of setIdUser
     *
     * @param int|null $idUser int
     *
     * @return $this
     */
    public function setIdUser(?int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Summary of getUserProfileImage
     *
     * @return string|null
     */
    public function getUserProfileImage(): ?string
    {
        return $this->userProfileImage;
    }

    /**
     * Summary of setUserProfileImage
     *
     * @param string $userProfileImage string
     *
     * @return $this
     */
    public function setUserProfileImage(string $userProfileImage): static
    {
        $this->userProfileImage = $userProfileImage;

        return $this;
    }


    /**
     * Summary of getContent
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Summary of setContent
     *
     * @param string $content string
     *
     * @return $this
     */
    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
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
     * @param int|null $idTrick int
     *
     * @return void
     */
    public function setIdTrick(?int $idTrick): void
    {
        $this->idTrick = $idTrick;
    }

    /**
     * Summary of setUsername
     *
     * @param string|null $username string
     *
     * @return void
     */
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    /**
     * Summary of getUsername
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
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
     * @return void
     */
    public function setTrick(?Trick $trick): void
    {
        $this->trick = $trick;
    }

    /**
     * Summary of getUser
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Summary of setUser
     *
     * @param User|null $user Object
     * 
     * @return void
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * Summary of getCreatedAt
     *
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Summary of setCreatedAt
     *
     * @param \DateTimeInterface|null $createdAt Object
     * 
     * @return void
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

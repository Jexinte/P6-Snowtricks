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
    private ?int $_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $_idUser = null;
    #[ORM\Column(nullable: true)]
    private ?int $_idTrick = null;

    #[ORM\Column(length: 255)]
    private ?string $_userProfileImage = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $_createdAt = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ ne peut être vide !')]
    #[Assert\Regex(
        pattern: '/^[A-ZÀ-ÿ][A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\_\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]{0,498}[A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\_\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]$/',
        message: 'Un commentaire 
    doit commencer par une lettre majuscule
     et ne peut excéder 500 caractères',
        match: true,
    )]
    private ?string $_content = null;

    private ?string $_username = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(name: 'id_trick', referencedColumnName: 'id', nullable: false)]
    private ?Trick $_trick = null;
    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id', nullable: false)]
    private ?User $_user = null;

    /**
     * Summary of getId
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->_id;
    }

    /**
     * Summary of getIdUser
     *
     * @return int|null
     */
    public function getIdUser(): ?int
    {
        return $this->_idUser;
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
        $this->_idUser = $idUser;

        return $this;
    }

    /**
     * Summary of getUserProfileImage
     *
     * @return string|null
     */
    public function getUserProfileImage(): ?string
    {
        return $this->_userProfileImage;
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
        $this->_userProfileImage = $userProfileImage;

        return $this;
    }


    /**
     * Summary of getContent
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->_content;
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
        $this->_content = $content;

        return $this;
    }

    /**
     * Summary of getIdTrick
     *
     * @return int|null
     */
    public function getIdTrick(): ?int
    {
        return $this->_idTrick;
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
        $this->_idTrick = $idTrick;
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
        $this->_username = $username;
    }

    /**
     * Summary of getUsername
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->_username;
    }

    /**
     * Summary of getTrick
     *
     * @return Trick|null
     */
    public function getTrick(): ?Trick
    {
        return $this->_trick;
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
        $this->_trick = $trick;
    }

    /**
     * Summary of getUser
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->_user;
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
        $this->_user = $user;
    }

    /**
     * Summary of getCreatedAt
     *
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->_createdAt;
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
        $this->_createdAt = $createdAt;
    }
}

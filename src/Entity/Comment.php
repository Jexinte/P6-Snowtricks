<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
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
    private ?\DateTimeInterface $dateCreation = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'content_exception',

        ]
    )]
    #[Assert\Regex(
        pattern: '/^[A-ZÀ-ÿ][A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\_\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]{0,498}[A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\_\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]$/',
        message: 'Un commentaire 
    doit commencer par une lettre majuscule
     et ne peut excéder 500 caractères',
        match: true,
        groups: ['content_wrong_format_exception']
    )]
    #[ORM\Column(length: 255)]
    private ?string $content = null;

    public string $username;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(?int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getUserProfileImage(): ?string
    {
        return $this->userProfileImage;
    }

    public function setUserProfileImage(string $userProfileImage): static
    {
        $this->userProfileImage = $userProfileImage;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdTrick(): ?int
    {
        return $this->idTrick;
    }

    /**
     * @param int|null $idTrick
     */
    public function setIdTrick(?int $idTrick): void
    {
        $this->idTrick = $idTrick;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }  public function getUsername():string
    {
        return $this->username;
    }
}

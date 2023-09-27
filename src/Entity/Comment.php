<?php

namespace App\Entity;

use App\Repository\CommentRepository;
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
    private ?\DateTimeInterface $createdAt = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Ce champ ne peut être vide !')]
    #[Assert\Regex(
        pattern: '/^[A-ZÀ-ÿ][A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\_\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]{0,498}[A-ZÀ-ÿa-zÀ-ÿ0-9\s\-\_\!\@\#\$\%\&\'\(\)\*\+\,\.\:\/\;\=\?\[\]\^\`\{\|\}\~]$/',
        message: 'Un commentaire 
    doit commencer par une lettre majuscule
     et ne peut excéder 500 caractères',
        match: true,
    )]
    private ?string $content = null;

    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(name:'id_trick',referencedColumnName: 'id',nullable: false)]
    private ?Trick $trick = null;
    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(name:'id_user',referencedColumnName: 'id',nullable: false)]
private ?User $user = null;
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

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }  public function getUsername():string
    {

        return $this->username;
    }

    /**
     * @return Trick|null
     */
    public function getTrick(): ?Trick
    {
        return $this->trick;
    }


    /**
     * @param Trick|null $trick
     */
    public function setTrick(?Trick $trick): void
    {
        $this->trick = $trick;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface|null $createdAt
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}

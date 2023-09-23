<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

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


    #[ORM\Column(length: 255)]
    private ?string $content = null;

    private ?string $username = null;

    #[ORM\ManyToOne(inversedBy: 'comment')]
    #[ORM\JoinColumn(name:'id_trick',referencedColumnName: 'id',nullable: false)]
    private ?Trick $trick = null;
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
}

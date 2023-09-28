<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]

#[UniqueEntity(fields: 'name',message: 'Oops ! Le nom d\'utilisateur n\'est pas disponible, veuillez en définir un autre !',groups: ['signUp'])]
#[UniqueEntity(fields: 'email',message: 'Oops ! L\'adresse email n\'est pas disponible, veuillez en définir un autre !',groups: ['signUp'])]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups:['signUp','login','forgotPassword','resetPassword'],
    )]
    #[Assert\Regex(
        pattern: '/^(?=[A-Z])([A-Za-z0-9]{1,10})$/',
        message: 'Le nom d\'utilisateur doit commencer par une majuscule , ne peut contenir que des chiffres et ne doit excéder 10 caractères !',
        match: true,
        groups:['signUp'],
    )]
    #[ORM\Column(length: 255,unique:true)]
    private ?string $name = null;


    #[ORM\Column(length: 255)]
    private ?string $profileImage;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
      message:'Ce champ ne peut être vide !',
        groups:['signUp'],
    )]
    #[Assert\Regex(
        pattern: '/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/',
        message: 'Oops! Le format de votre saisie est incorrect,merci de suivre le format requis : nomadressemail@domaine.extension',
        match: true,
        groups:['signUp'],
    )]
    private ?string $email = null;

    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups:['signUp','login','resetPassword'],
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*\d).{8,}$/',
        message: 'Oops! Le format de votre mot de passe est incorrect, il doit être composé d\'une lettre majuscule , d\'un chiffre et 8 caractères minimum !',
        match: true,
        groups:['signUp','resetPassword'],
    )]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 1)]
    private ?bool $status = null;

    private ?bool $created;
#[Assert\File(
    maxSize: '3000K',
    extensions: ['jpg', 'png', 'webp'],
    extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !',
)]
#[Assert\NotBlank(message: 'Veuillez sélectionner un fichier !',groups: ['signUp'])]
    private UploadedFile $file;

    private ?int $userId = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups:['resetPassword'],
    )]
    private string $oldPassword;

    private ?bool $credentialsValid = null;

    private ?bool $nameExist = null;
    private ?bool $passwordIsCorrect = null;
    private ?bool $accountIsActivate = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, cascade: ['persist'])]
    private Collection $comment;
    public function __construct()
    {
        $this->comment = new ArrayCollection();
    }

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

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(string $profileImage): static
    {
        $this->profileImage = $profileImage;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }


    public  function getStatus():?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function isCreated(): ?bool
    {
        if (!is_null($this->created)) {
            return $this->created;
        }
        return null;
    }

    public function setCreated(bool $created): void
    {
        $this->created = $created;
    }

    public function getOldPassword():string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword):void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return bool|null
     */
    public function getCredentialsValid(): ?bool
    {
        return $this->credentialsValid;
    }

    /**
     * @param bool|null $credentialsValid
     */
    public function isCredentialsValid(?bool $credentialsValid): void
    {
        $this->credentialsValid = $credentialsValid;
    }

    /**
     * @return bool|null
     */
    public function getNameExist(): ?bool
    {
        return $this->nameExist;
    }

    /**
     * @param bool|null $nameExist
     */
    public function isNameExist(?bool $nameExist): void
    {
        $this->nameExist = $nameExist;
    }


    /**
     * @param bool|null $passwordIsCorrect
     */
    public function isPasswordCorrect(?bool $passwordIsCorrect): void
    {
        $this->passwordIsCorrect = $passwordIsCorrect;
    }

    public function getPasswordCorrect():?bool
    {
        return $this->passwordIsCorrect;
    }

    /**
     * @return bool|null
     */
    public function getAccountIsActivate(): ?bool
    {
        return $this->accountIsActivate;
    }

    /**
     * @param bool|null $accountIsActivate
     */
    public function isAccountActivate(?bool $accountIsActivate): void
    {
        $this->accountIsActivate = $accountIsActivate;
    }

    /**
     * @return int
     */
    public function getUserId():?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getComment(): Collection
    {
        return $this->comment;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comment->contains($comment)) {
            $this->comment->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }



}

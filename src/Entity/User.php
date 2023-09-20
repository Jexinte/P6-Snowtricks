<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: 'name',
    message: "Le nom utilisateur est déjà pris !",
)]
#[UniqueEntity(
    fields: 'email',
    message: "L'adresse email n'est pas disponible, merci d'en sélectionner une autre !",
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'username_exception',
            "username_exception_sign_in",
            "username_exception_forgot_password",
            "username_exception_reset_password"
        ]
    )]
    #[Assert\Regex(
        pattern: '/^(?=[A-Z])([A-Za-z0-9]{1,10})$/',
        message: 'Le nom d\'utilisateur doit commencer par une majuscule , ne peut contenir que des chiffres et ne doit excéder 10 caractères !',
        match: true,
        groups: ['username_exception']
    )]
    #[ORM\Column(length: 255,unique:true)]
    private ?string $name = null;


    #[ORM\Column(length: 255)]
    private ?string $profileImage;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: [
            'password_exception',
            "password_exception_sign_in",
            "password_exception_old_reset_password",
            "password_exception_new_reset_password"
        ]
    )]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 1)]
    private ?bool $status = null;

    private ?bool $created;

    private UploadedFile $file;


    private string $oldPassword;

    private ?bool $credentialsValid = null;

    private ?bool $nameExist = null;
    private ?bool $passwordIsCorrect = null;
    private ?bool $accountIsActivate = null;

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


}

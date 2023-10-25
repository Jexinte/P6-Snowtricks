<?php
/**
 * Handle user properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  User
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: 'username', message: 'Oops ! Le nom d\'utilisateur n\'est pas disponible, veuillez en définir un autre !', groups: ['signUp'])]
#[UniqueEntity(fields: 'email', message: 'Oops ! L\'adresse email n\'est pas disponible, veuillez en définir un autre !', groups: ['signUp'])]

/**
 * Handle user properties
 *
 * PHP version 8
 *
 * @category Entity
 * @package  User
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $_id = null;

    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['signUp', 'forgotPassword', 'resetPassword'],
    )]
    #[Assert\Regex(
        pattern: '/^(?=[A-Z])([A-Za-z0-9]{1,10})$/',
        message: 'Le nom d\'utilisateur doit commencer par une majuscule , ne peut contenir que des chiffres et ne doit excéder 10 caractères !',
        match: true,
        groups: ['signUp'],
    )]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $_username = null;


    #[ORM\Column(length: 255)]
    private ?string $_profileImage;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['signUp'],
    )]
    #[Assert\Regex(
        pattern: '/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/',
        message: 'Oops! Le format de votre saisie est incorrect,merci de suivre le format requis : nomadressemail@domaine.extension',
        match: true,
        groups: ['signUp'],
    )]
    private ?string $_email = null;

    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['signUp', 'resetPassword'],
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*\d).{8,}$/',
        message: 'Oops! Le format de votre mot de passe est incorrect, il doit être composé d\'une lettre majuscule , d\'un chiffre et 8 caractères minimum !',
        match: true,
        groups: ['signUp', 'resetPassword'],
    )]
    #[ORM\Column(length: 255)]
    private ?string $_password = null;

    #[ORM\Column(length: 1)]
    private ?bool $_status = null;


    #[ORM\Column(type: 'json')]
    private array $_roles = [];
    private ?bool $_created;
    #[Assert\File(
        maxSize: '3000K',
        groups: ['signUp'],
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un fichier !', groups: ['signUp'])]
    private UploadedFile $_file;

    private ?int $_userId = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['resetPassword'],
    )]
    private string $_oldPassword;

    private ?bool $_credentialsValid = null;

    private ?bool $_nameExist = null;
    private ?bool $_passwordIsCorrect = null;
    private ?bool $_accountIsActivate = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, cascade: ['persist'])]
    private Collection $_comment;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->_comment = new ArrayCollection();
    }

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
     * Summary of getUsername
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->_username;
    }

    /**
     * Summary of setUsername
     *
     * @param string $username string
     *
     * @return $this
     */
    public function setUsername(string $username): static
    {
        $this->_username = $username;

        return $this;
    }

    /**
     * Summary of getUserIdentifier
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->_username;
    }

    /**
     * Summary of getRoles
     *
     * @see UserInterface
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->_roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * Summary of setRoles
     *
     * @param array<string> $roles array
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->_roles = $roles;

        return $this;
    }

    /**
     * Summary of eraseCredentials
     *
     * @see UserInterface
     *
     * @return void
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * Summary of getProfileImage
     *
     * @return string|null
     */
    public function getProfileImage(): ?string
    {
        return $this->_profileImage;
    }

    /**
     * Summary of setProfileImage
     *
     * @param string $profileImage string
     *
     * @return $this
     */
    public function setProfileImage(string $profileImage): static
    {
        $this->_profileImage = $profileImage;

        return $this;
    }

    /**
     * Summary of getFile
     *
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->_file;
    }

    /**
     * Summary of setFile
     *
     * @param UploadedFile $file Object
     *
     * @return $this
     */
    public function setFile(UploadedFile $file): static
    {
        $this->_file = $file;

        return $this;
    }

    /**
     * Summary of getEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->_email;
    }

    /**
     * Summary of setEmail
     *
     * @param string $email string
     *
     * @return $this
     */
    public function setEmail(string $email): static
    {
        $this->_email = $email;

        return $this;
    }

    /**
     * Summary of getPassword
     *
     * @return string|null
     * @see    PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->_password;
    }

    /**
     * Summary of setPassword
     *
     * @param string $password string
     *
     * @return $this
     */
    public function setPassword(string $password): static
    {
        $this->_password = $password;

        return $this;
    }


    /**
     * Summary of getStatus
     *
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->_status;
    }

    /**
     * Summary of setStatus
     *
     * @param bool $status bool
     *
     * @return $this
     */
    public function setStatus(bool $status): static
    {
        $this->_status = $status;
        return $this;
    }

    /**
     * Summary of isCreated
     *
     * @return bool|null
     */
    public function isCreated(): ?bool
    {
        if (!is_null($this->_created)) {
            return $this->_created;
        }
        return null;
    }

    /**
     * Summary of setCreated
     *
     * @param bool $created bool
     *
     * @return void
     */
    public function setCreated(bool $created): void
    {
        $this->_created = $created;
    }

    /**
     * Summary of getOldPassword
     *
     * @return string
     */
    public function getOldPassword(): string
    {
        return $this->_oldPassword;
    }

    /**
     * Summary of setOldPassword
     *
     * @param string $oldPassword string
     *
     * @return void
     */
    public function setOldPassword(string $oldPassword): void
    {
        $this->_oldPassword = $oldPassword;
    }

    /**
     * Summary of getCredentialsValid
     *
     * @return bool|null
     */
    public function getCredentialsValid(): ?bool
    {
        return $this->_credentialsValid;
    }

    /**
     * Summary of isCredentialsValid
     *
     * @param bool|null $credentialsValid ?bool
     *
     * @return void
     */
    public function isCredentialsValid(?bool $credentialsValid): void
    {
        $this->_credentialsValid = $credentialsValid;
    }

    /**
     * Summary of getUsernameExist
     *
     * @return bool|null
     */
    public function getUsernameExist(): ?bool
    {
        return $this->_nameExist;
    }

    /**
     * Summary of isNameExist
     *
     * @param bool|null $nameExist ?bool
     *
     * @return void
     */
    public function isNameExist(?bool $nameExist): void
    {
        $this->_nameExist = $nameExist;
    }


    /**
     * Summary of isPasswordCorrect
     *
     * @param bool|null $passwordIsCorrect ?bool
     *
     * @return void
     */
    public function isPasswordCorrect(?bool $passwordIsCorrect): void
    {
        $this->_passwordIsCorrect = $passwordIsCorrect;
    }

    /**
     * Summary of getPasswordCorrect
     *
     * @return bool|null
     */
    public function getPasswordCorrect(): ?bool
    {
        return $this->_passwordIsCorrect;
    }

    /**
     * Summary of getAccountIsActivate
     *
     * @return bool|null
     */
    public function getAccountIsActivate(): ?bool
    {
        return $this->_accountIsActivate;
    }

    /**
     * Summary of isAccountActivate
     *
     * @param bool|null $accountIsActivate ?bool
     *
     * @return void
     */
    public function isAccountActivate(?bool $accountIsActivate): void
    {
        $this->_accountIsActivate = $accountIsActivate;
    }

    /**
     * Summary of getUserId
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->_userId;
    }

    /**
     * Summary of setUserId
     *
     * @param int|null $userId int
     *
     * @return void
     */
    public function setUserId(?int $userId): void
    {
        $this->_userId = $userId;
    }

    /**
     * Summary of getComment
     *
     * @return Collection
     */
    public function getComment(): Collection
    {
        return $this->_comment;
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
        if (!$this->_comment->contains($comment)) {
            $this->_comment->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }


}

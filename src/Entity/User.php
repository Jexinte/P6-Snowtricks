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
    private ?int $id = null;

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
    private ?string $username = null;


    #[ORM\Column(length: 255)]
    private ?string $profileImage;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['signUp'],
    )]
    #[Assert\Regex(
        pattern: '/^[a-z0-9.-]+@[a-z0-9.-]{2,}\.[a-z]{2,4}$/',
        message: 'Oops! Le format de votre saisie est incorrect,merci de suivre le format requis : nomadressemail@domaine.extension',
        match: true,
        groups: ['signUp'],
    )]
    private ?string $email = null;

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
    private ?string $password = null;

    #[ORM\Column(length: 1)]
    private ?bool $status = null;


    #[ORM\Column(type: 'json')]
    private array $roles = [];
    private ?bool $created;
    #[Assert\File(
        maxSize: '3000K',
        groups: ['signUp'],
        extensions: ['jpg', 'png', 'webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    #[Assert\NotBlank(message: 'Veuillez sélectionner un fichier !', groups: ['signUp'])]
    private UploadedFile $file;

    private ?int $userId = null;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['resetPassword'],
    )]
    private string $oldPassword;

    private ?bool $credentialsValid = null;

    private ?bool $nameExist = null;
    private ?bool $passwordIsCorrect = null;
    private ?bool $accountIsActivate = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, cascade: ['persist'])]
    private Collection $comment;

    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->comment = new ArrayCollection();
    }

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
     * Summary of getUsername
     *
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
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
        $this->username = $username;

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
        return (string)$this->username;
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
        $roles = $this->roles;
        $roles[] = 'ROLEUSER';
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
        $this->roles = $roles;

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
        return $this->profileImage;
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
        $this->profileImage = $profileImage;

        return $this;
    }

    /**
     * Summary of getFile
     *
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
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
        $this->file = $file;

        return $this;
    }

    /**
     * Summary of getEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
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
        $this->email = $email;

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
        return $this->password;
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
        $this->password = $password;

        return $this;
    }


    /**
     * Summary of getStatus
     *
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
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
        $this->status = $status;
        return $this;
    }

    /**
     * Summary of isCreated
     *
     * @return bool|null
     */
    public function isCreated(): ?bool
    {
        if (!is_null($this->created)) {
            return $this->created;
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
        $this->created = $created;
    }

    /**
     * Summary of getOldPassword
     *
     * @return string
     */
    public function getOldPassword(): string
    {
        return $this->oldPassword;
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
        $this->oldPassword = $oldPassword;
    }

    /**
     * Summary of getCredentialsValid
     *
     * @return bool|null
     */
    public function getCredentialsValid(): ?bool
    {
        return $this->credentialsValid;
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
        $this->credentialsValid = $credentialsValid;
    }

    /**
     * Summary of getUsernameExist
     *
     * @return bool|null
     */
    public function getUsernameExist(): ?bool
    {
        return $this->nameExist;
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
        $this->nameExist = $nameExist;
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
        $this->passwordIsCorrect = $passwordIsCorrect;
    }

    /**
     * Summary of getPasswordCorrect
     *
     * @return bool|null
     */
    public function getPasswordCorrect(): ?bool
    {
        return $this->passwordIsCorrect;
    }

    /**
     * Summary of getAccountIsActivate
     *
     * @return bool|null
     */
    public function getAccountIsActivate(): ?bool
    {
        return $this->accountIsActivate;
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
        $this->accountIsActivate = $accountIsActivate;
    }

    /**
     * Summary of getUserId
     *
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
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
        $this->userId = $userId;
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
            $comment->setUser($this);
        }

        return $this;
    }


}

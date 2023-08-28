<?php


namespace App\Controller\DTO;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['username_exception',"username_exception_sign_in","username_exception_forgot_password","username_exception_reset_password"]
    )]
    #[Assert\Regex(
        pattern: '/^(?=[A-Z])([A-Za-z0-9]{1,10})$/',
        message: 'Le nom d\'utilisateur doit commencer par une majuscule , ne peut contenir que des chiffres et ne doit excéder 10 caractères !',
        match: true,
        groups: ['username_exception']
 )]
    protected string $name;

    #[Assert\File(
        maxSize: '3000K',
        groups: ['file_exception'],
        extensions: ['jpg','png','webp'],
        extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png et webp sont acceptés !'
    )]
    #[Assert\NotBlank(
        message: 'Veuillez sélectionner un fichier !',
        groups: ['file_exception']
    )]
    protected UploadedFile $file;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['email_exception']
    )]

    #[Assert\Regex(
        pattern: '/^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/',
        message: 'Oops! Le format de votre saisie est incorrect,merci de suivre le format requis : nomadressemail@domaine.extension',
        match: true,
        groups: ['email_exception']
    )]
    protected string $email;
    #[Assert\NotBlank(
        message: 'Ce champ ne peut être vide !',
        groups: ['password_exception',"password_exception_sign_in","password_exception_old_reset_password","password_exception_new_reset_password"]
    )]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*\d).{8,}$/',
        message: 'Oops! Le format de votre mot de passe est incorrect, il doit être composé d\'une lettre majuscule , d\'un chiffre et 8 caractères minimum !',
        match: true,
        groups: ['password_exception',"password_exception_forgot_password","password_exception_wrong_format"]
    )]
    protected string $password;

    protected string $oldPassword;

    protected  ?bool $created = null;
    public function getName(): string
    {
        return $this->name;
    }


    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFile():?UploadedFile
    {

        return $this->file;
    }

    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isCreated():?bool
    {

        if(!is_null($this->created)) return $this->created;
        return null;
    }
    public function setCreated(bool $created):void
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
}

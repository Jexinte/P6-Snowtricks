<?php

namespace App\Repository;

use App\Controller\DTO\UserDTO;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Enumeration\UserStatus;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * @param UserDTO $userDto
     * @return string[]|null
     * @throws \Exception
     */
    public function createUser(UserDTO $userDto):?array
{
$user = new User();
$entityManager = $this->getEntityManager();
$usernameNotAvailable = $this->findOneBy(["name" => $userDto->getName()]);
$emailNotAvailable = $this->findOneBy(["email" => $userDto->getEmail()]);
switch (true){
    case $usernameNotAvailable:
        return [
            'username_unavailable' => 'Le nom utilisateur '.$usernameNotAvailable->getName().' n\'est pas disponible',
        ];

    case $emailNotAvailable:
        return [
            'email_unavailable' => "L'adresse email ". $emailNotAvailable->getEmail() ." n'est pas disponible"
        ];
    default:
        $fileExt = explode('.',$userDto->getFile()->getClientOriginalName());
        $filename = str_replace("/", "", base64_encode(random_bytes(9))).'.'.$fileExt[1];
        $imgPath = "http://localhost:8000/Snowtricks/public/assets/img/$filename";
        $user->setName($userDto->getName());
        $user->setProfileImage($imgPath);
        $user->setEmail($userDto->getEmail());
        $user->setPassword($userDto->getPassword());
        $user->setStatus(UserStatus::ACCOUNT_NOT_ACTIVATE);
        $tmp = $userDto->getFile()->getPathname();
        $dir = "../public/assets/img";
        move_uploaded_file($tmp,"$dir/$filename");
        $entityManager->persist($user);
        $entityManager->flush();

}

return null;
}

public function updateUserStatus(Request $request): bool
{
    $usernameInSession = $request->getSession()->get('username');
    $entityManager = $this->getEntityManager();
    $dataToUpdate = $entityManager->getRepository(User::class)->findBy(["name" => $usernameInSession]);
    foreach ($dataToUpdate as $record){
        $record->setStatus(UserStatus::ACCOUNT_ACTIVATE);
    }
    $entityManager->flush();

    return true;
}

    /**
     * @param UserDTO $userDTO
     * @param Request $request
     * @return array<string>|bool
     */
    public function login(UserDTO $userDTO,Request $request):array|bool
{
    $usernameFromForm = $userDTO->getName();
    $passwordFromForm = $userDTO->getPassword();

    $userInDb = current($this->findBy(["name" => $usernameFromForm]));
    return match (true) {
        !$userInDb => ["username_failed" => "Oops ! Il semblerait que le nom d'utilisateur $usernameFromForm n'existe pas !"],
        $userInDb->getName() == $usernameFromForm && password_verify(
            $passwordFromForm,
            $userInDb->getPassword()
        ) => [
            "connected" => UserStatus::CONNECTED,
            "user_id" => $userInDb->getId(),
        ],
        default => ["password_failed" => "Oops ! Il semblerait que le mot de passe saisi est incorrect !"],
    };


}

public function checkUser(UserDTO $userDto):?User
{
    $userMatch = $this->findOneBy(["name" => $userDto->getName()]);
    if($userMatch) {
        return $userMatch;
    }
    return null;
}

public function checkPasswordReset(UserDTO $userDto):?bool
{
    $userDataFromDb = $this->checkUser($userDto);
    $oldPasswordFromForm = $userDto->getOldPassword();
    $newPasswordFromForm = $userDto->getPassword();
    if(password_verify($oldPasswordFromForm,$userDataFromDb->getPassword()))
    {
        $entityManager = $this->getEntityManager();
        $dataToUpdate = $entityManager->getRepository(User::class)->findBy(["name" => $userDataFromDb->getName()]);
        foreach ($dataToUpdate as $record){
            $record->setPassword(password_hash($newPasswordFromForm,PASSWORD_DEFAULT));
        }
        $entityManager->flush();

    }
    return true;

}
}

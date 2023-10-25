<?php

/**
 * Handle user repository
 *
 * PHP version 8
 *
 * @category Repository
 * @package  UserRepository
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Enumeration\UserStatus;

/**
 * Details of ServiceEntityRepository possibilities
 *
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * Handle user repository
 *
 * PHP version 8
 *
 * @category Repository
 * @package  UserRepository
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * Summary of __construct
     *
     * @param ManagerRegistry $registry Object
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    /**
     * Summary of getEntityManager
     *
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return parent::getEntityManager();
    }

    /**
     * Summary of updateUserStatus
     *
     * @param Request $request Request
     *
     * @return bool
     */
    public function updateUserStatus(Request $request): bool
    {
        $usernameInSession = $request->getSession()->get('username');
        $entityManager = $this->getEntityManager();
        $dataToUpdate = $entityManager->getRepository(User::class)->findBy(["username" => $usernameInSession]);
        foreach ($dataToUpdate as $record) {
            $record->setStatus(UserStatus::ACCOUNT_ACTIVATE);
        }
        $entityManager->flush();

        return true;
    }


    /**
     * Summary of checkPasswordReset
     *
     * @param User $user Object
     * 
     * @return User
     */
    public function checkPasswordReset(User $user): User
    {
        $userDataFromDb = current($this->findBy(["username" => $user->getUsername()]));
        $oldPasswordFromForm = $user->getOldPassword();
        $newPasswordFromForm = $user->getPassword();
        if (is_object($userDataFromDb)) {
            if (password_verify($oldPasswordFromForm, $userDataFromDb->getPassword())) {
                $dataToUpdate = $this->getEntityManager()->getRepository(User::class)->findBy(
                    ["username" => $userDataFromDb->getUsername()]
                );
                foreach ($dataToUpdate as $record) {
                    $record->setPassword(password_hash($newPasswordFromForm, PASSWORD_DEFAULT));
                }
                $this->getEntityManager()->flush();
                $user->isCredentialsValid(true);
            } else {
                $user->isPasswordCorrect(false);
            }
        } else {
            $user->isCredentialsValid(false);
        }
        return $user;
    }
}

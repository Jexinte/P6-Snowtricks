<?php

/**
 * Handle comment repository
 *
 * PHP version 8
 *
 * @category Repository
 * @package  CommentRepository
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Details of ServiceEntityRepository possibilities
 *
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * Handle comment repository
 *
 * PHP version 8
 *
 * @category Repository
 * @package  CommentRepository
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class CommentRepository extends ServiceEntityRepository
{

    /**
     * Summary of __construct
     *
     * @param ManagerRegistry $registry Object
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
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
     * Summary of getComments
     *
     * @param int            $id             int
     * @param UserRepository $userRepository Object
     *
     * @return array
     */
    public function getComments(int $id, UserRepository $userRepository): array
    {
        $comments = $this->findBy(["idTrick" => $id]);

        foreach ($comments as $comment) {
            $comment->setUsername(
                $userRepository->find(["id" => $comment->getIdUser()])
                    ->getUsername()
            );
        }
        return $comments;
    }


    /**
     * Summary of getCommentsPerPage
     *
     * @param int $id             int
     * @param int $firstPage      int
     * @param int $commentPerPage int
     *
     * @return array
     */
    public function getCommentsPerPage(
        int $id, int $firstPage, int $commentPerPage
    ): array {
        $orderBy = ['createdAt' => 'DESC'];

        $offset = $firstPage;
        $limit = $commentPerPage;

        return $this->findBy(["idTrick" => $id], $orderBy, $limit, $offset);
    }

}

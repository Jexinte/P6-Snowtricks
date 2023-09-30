<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    public function getEntityManager()
    {
        return parent::getEntityManager();
    }


    /**
     * @param int $id
     * @param UserRepository $userRepository
     * @return Comment[]
     */
    public function getComments(int $id, UserRepository $userRepository): array
    {
        $comments = $this->findBy(["idTrick" => $id], );

        foreach ($comments as $k => $object) {
            $comments[$k]->setUsername($userRepository->find(["id" => $object->getIdUser()])->getUsername());
        }
        return $comments;
    }

    /**
     * @param int $id
     * @param int $firstPage
     * @param int $commentPerPage
     * @return Comment[]
     */
    public function getCommentsPerPage(int $id, int $firstPage, int $commentPerPage): array
    {
        $orderBy = ['createdAt' => 'DESC'];

        $offset = $firstPage;
        $limit = $commentPerPage;

        return $this->findBy(["idTrick" => $id], $orderBy, $limit, $offset);
    }

}

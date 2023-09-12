<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\User;
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
     * @param Comment $comment
     * @return bool
     */
    public function saveComment(Comment $comment):bool
    {
        $this->getEntityManager()->persist($comment);
        $this->getEntityManager()->flush();
        return true;
    }

    /**
     * @param int $id
     * @param UserRepository $userRepository
     * @return Comment[]
     */
    public function getComments(int $id,UserRepository $userRepository): array
    {
        $comments = $this->findBy(["idTrick" => $id]);
        foreach($comments as $k => $object)
        {
            $comments[$k]->setUsername($userRepository->find(["id" => $object->getIdUser()])->getName());
        }

        return $comments;

    }

}

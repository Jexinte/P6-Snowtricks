<?php

namespace App\Repository;

use App\Controller\DTO\TrickDTO;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }


    /**
     * @return Trick[]
     */
    public function getTricks(): array
    {
        $this->getEntityManager()->getRepository(Trick::class);
        return $this->findAll();
    }


    /**
     * @param TrickDTO $trickDTO
     * @return false[]|true[]|null
     */
    public function deleteTrick(TrickDTO $trickDTO): ?array
    {
        $getTrick = $this->find(["id" => $trickDTO->getId()]);
        if (!is_null($getTrick)) {
            $this->getEntityManager()->remove($getTrick);
            $this->getEntityManager()->flush();
            return ["trick_delete" => true];
        }
        return ["trick_delete" => false];
    }


}

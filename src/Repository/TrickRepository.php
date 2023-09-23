<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function getEntityManager(): EntityManagerInterface
    {
        return parent::getEntityManager();
    }

    public function updateTrick(int $id, Trick $trick): void
    {
        $trickNameFromForm = $trick->getNameUpdated();
        $trickDescription = $trick->getDescription();
        $trickGroup = $trick->getTrickGroup();
        $dataToUpdate = $this->getEntityManager()->getRepository(Trick::class)->findBy(["id" => $id]);


        foreach ($dataToUpdate as $record) {
            $record->setName($trickNameFromForm);
            $record->setDescription($trickDescription);
            $record->setTrickGroup($trickGroup);
            $record->isTrickUpdated($trick->getTrickUpdated());
        }

        $this->getEntityManager()->flush();
    }
}

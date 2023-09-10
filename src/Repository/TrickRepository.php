<?php

namespace App\Repository;

use App\Entity\Media;
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

    public function getEntityManager()
    {
        return parent::getEntityManager();
    }

    public function getTrick(int $id): ?Trick
    {
        return $this->findOneBy(["id" => $id]);
    }


    public function createTrick(Trick $trick): int
    {
        $this->getEntityManager()->persist($trick);
        $this->getEntityManager()->flush();
        $tricks = $this->findAll();
        $trick = end($tricks);
        return $trick->getId();
    }

    public function updateTrick(int $id, Trick $trick): bool
    {
        $trickName = $trick->getNameUpdated();
        $trickDescription = $trick->getDescription();
        $trickGroup = $trick->getTrickGroup();
        $trickNameFromDb = $this->getTrick($id)->getName();
        $dataToUpdate = $this->getEntityManager()->getRepository(Trick::class)->findBy(["id" => $id]);

        if ($trickName == $trickNameFromDb) {
            foreach ($dataToUpdate as $record) {
                $record->setDescription($trickDescription);
                $record->setTrickGroup($trickGroup);
            }
        } else {
            foreach ($dataToUpdate as $record) {
                $record->setName($trickName);
                $record->setDescription($trickDescription);
                $record->setTrickGroup($trickGroup);
            }
        }
        $this->getEntityManager()->flush();
        return true;
    }
}

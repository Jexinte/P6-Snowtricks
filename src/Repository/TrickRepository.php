<?php

/**
 * Handle trick repository
 *
 * PHP version 8
 *
 * @category Repository
 * @package  TrickRepository
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Details of ServiceEntityRepository possibilities
 *
 * @extends ServiceEntityRepository<Trick>
 *
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

/**
 * Handle trick repository
 *
 * PHP version 8
 *
 * @category Repository
 * @package  TrickRepository
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class TrickRepository extends ServiceEntityRepository
{
    /**
     * Summary of __construct
     *
     * @param ManagerRegistry $registry Object
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    /**
     * Summary of getEntityManager
     * 
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return parent::getEntityManager();
    }


}

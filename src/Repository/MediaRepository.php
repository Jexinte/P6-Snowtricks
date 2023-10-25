<?php

/**
 * Handle media repository
 *
 * PHP version 8
 *
 * @category Repository
 * @package  MediaRepository
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Details of ServiceEntityRepositoy possibilities
 *
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
class MediaRepository extends ServiceEntityRepository
{
    /**
     * Summary of __construct
     *
     * @param ManagerRegistry $registry Object
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
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


}

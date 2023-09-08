<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }

    public function getEntityManager()
    {
        return parent::getEntityManager();
    }

    public function saveTrickMedias(Media $media)
    {
        $images = $media->getImages();
        $videos = $media->getVideos();
        $dirImages = "../public/assets/img";
        $dirVideos = "../public/assets/videos";
        foreach ($images as $k => $image) {
            $fileExt = explode('.', $image->getClientOriginalName());
            $filename = str_replace("/", "", base64_encode(random_bytes(9))) . '.' . $fileExt[1];
            $imgPath = "/assets/img/$filename";
            $tmp = $image->getPathname();
            $media->setMediaPath($imgPath);
            $media->setMediaType($fileExt[1]);
            move_uploaded_file($tmp, "$dirImages/$filename");
            $this->getEntityManager()->persist($media);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
        }
        foreach ($videos as $k => $video) {
            $fileExt = explode('.', $video->getClientOriginalName());
            $filename = str_replace("/", "", base64_encode(random_bytes(9))) . '.' . $fileExt[1];
            $videoPath = "/assets/videos/$filename";
            $tmp = $video->getPathname();
            $media->setMediaPath($videoPath);
            $media->setMediaType($fileExt[1]);
            move_uploaded_file($tmp, "$dirVideos/$filename");
            $this->getEntityManager()->persist($media);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
        }
    }

    /**
     * @param int $id
     * @return Media[]
     */
    public function getTrickMedia(int $id): array
    {
        return $this->findBy(["idTrick" => $id]);
    }
}

<?php

namespace App\Repository;

use App\Entity\Media;
use App\Entity\Trick;
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




    public function updateTrickMedia(int $id, Media $media): ?bool
    {
        $embedUrl = $media->getEmbedUrl();
        $file = $media->getUpdatedFile();
        switch (true) {
            case !empty($file):

                $fileExt = explode('.', $file->getClientOriginalName());
                $filePathInDb = current($this->findBy(["id" => $id]));
                $dir = "";
                $filePath = "";
                $filename = str_replace("/", "", base64_encode(random_bytes(9))) . '.' . $fileExt[1];
                if (in_array($fileExt[1], array("jpg", 'webp', "png")) && !$filePathInDb->getIsBanner()) {
                    $dir = "../public/assets/img";
                    $filePath = "/assets/img/$filename";
                } elseif ($fileExt[1] == "mp4") {
                    $dir = "../public/assets/videos";
                    $filePath = "/assets/videos/$filename";
                } else {
                    $dir = "../public/assets/img/banner";
                    $filePath = "/assets/img/banner/$filename";
                }
                unlink("../public" . $filePathInDb->getMediaPath());

                $tmp = $file->getPathname();

                $dataToUpdate = $this->getEntityManager()->getRepository(Media::class)->findBy(["id" => $id]);
                foreach ($dataToUpdate as $record) {
                    $record->setMediaPath($filePath);
                    $record->setMediaType($fileExt[1]);
                }
                $this->getEntityManager()->flush();
                move_uploaded_file($tmp, "$dir/$filename");
                return true;
            case !empty($embedUrl):
                $dataToUpdate = $this->getEntityManager()->getRepository(Media::class)->findBy(["id" => $id]);
                foreach ($dataToUpdate as $record) {
                    $record->setMediaPath($embedUrl);
                    $record->setMediaType("web");
                }
                $this->getEntityManager()->flush();
                return true;
        }
        return null;
    }
}

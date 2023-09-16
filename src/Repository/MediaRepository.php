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

    /**
     * @param Media $media
     * @return void
     */
    public function saveTrickMedias(Media $media)
    {
        $images = $media->getIllustrations();
        $videos = $media->getVideos();
        $dirImages = "../public/assets/img";
        $dirImagesBanner = "../public/assets/img/banner";
        $dirVideos = "../public/assets/videos";
        $bannerFile = $media->getBannerFile();
        $embedUrl = $media->getEmbedUrl();
        if (!empty($bannerFile)) {
            $fileExt = explode('.', $bannerFile->getClientOriginalName());
            $filename = str_replace("/", "", base64_encode(random_bytes(9))) . '.' . $fileExt[1];
            $imgBannerPath = "/assets/img/banner/$filename";
            $tmp = $bannerFile->getPathname();
            move_uploaded_file($tmp, "$dirImagesBanner/$filename");
            $media->setMediaPath($imgBannerPath);
            $media->setMediaType($fileExt[1]);
            $media->setIsBanner(true);
            $this->getEntityManager()->persist($media);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
        }
        if (!empty($embedUrl)) {
            $media->setMediaPath($embedUrl);
            $media->setMediaType("web");
            $media->setIsBanner(null);
            $this->getEntityManager()->persist($media);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
        }

        foreach ($images as $k => $image) {
            $fileExt = explode('.', $image->getClientOriginalName());
            $filename = str_replace("/", "", base64_encode(random_bytes(9))) . '.' . $fileExt[1];
            $imgPath = "/assets/img/$filename";
            $tmp = $image->getPathname();
            $media->setMediaPath($imgPath);
            $media->setMediaType($fileExt[1]);
            $media->setIsBanner(null);
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
            $media->setIsBanner(null);
            move_uploaded_file($tmp, "$dirVideos/$filename");
            $this->getEntityManager()->persist($media);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->clear();
        }
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

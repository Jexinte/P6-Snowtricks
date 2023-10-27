<?php

/**
 * Handle files of a trick created
 *
 * PHP version 8
 *
 * @category Service
 * @package  FileGenerator
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Service;

use App\Entity\Media;
use App\Entity\Trick;
use App\Repository\MediaRepository;

/**
 * Handle files of a trick created
 *
 * PHP version 8
 *
 * @category Service
 * @package  FileGenerator
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class FileGenerator
{
    /**
     * Summary of saveBanner
     *
     * @param Media           $media           Object
     * @param Trick           $trick           Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return void
     *
     * @throws \Exception
     */
    public function saveBanner(
        Media $media, Trick $trick, MediaRepository $mediaRepository
    ): void {
        $dirImagesBanner = "../public/assets/img/banner";
        $bannerFile = $media->getBannerFile();
        $fileExt = explode('.', $bannerFile->getClientOriginalName());
        $filename = str_replace(
                "/",
                "",
                base64_encode(random_bytes(9))
            ) . '.' . $fileExt[1];
        $imgBannerPath = "/assets/img/banner/$filename";
        $tmp = $bannerFile->getPathname();
        move_uploaded_file($tmp, "$dirImagesBanner/$filename");
        $media->setMediaPath($imgBannerPath);
        $media->setMediaType($fileExt[1]);
        $media->setIsBanner(true);
        $trick->addMedia($media);
        $mediaRepository->getEntityManager()->persist($media);
    }

    /**
     * Summary of saveEmbedUrl
     *
     * @param Media           $media           Object
     * @param Trick           $trick           Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return void
     */
    public function saveEmbedUrl(
        Media $media, Trick $trick, MediaRepository $mediaRepository
    ): void {
        $embedUrl = $media->getEmbedUrl();

        if (!empty($embedUrl)) {
            $newMedia = new Media();
            $newMedia->setMediaPath($embedUrl);
            $newMedia->setMediaType("web");
            $newMedia->setIsBanner();
            $trick->addMedia($newMedia);
            $mediaRepository->getEntityManager()->persist($newMedia);
        }
    }

    /**
     * Summary of saveImages
     *
     * @param Media           $media           Object
     * @param Trick           $trick           Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return void
     *
     * @throws \Exception
     */
    public function saveImages(
        Media $media, Trick $trick, MediaRepository $mediaRepository
    ): void {
        $images = $media->getImages();
        $dirImages = "../public/assets/img";
        if (!empty($media->getImages())) {
            foreach ($images as $image) {
                $newMedia = new Media();
                $fileExt = explode('.', $image->getClientOriginalName());
                $filename = str_replace(
                        "/",
                        "",
                        base64_encode(random_bytes(9))
                    ) . '.' . $fileExt[1];
                $imgPath = "/assets/img/$filename";
                $tmp = $image->getPathname();
                $newMedia->setMediaPath($imgPath);
                $newMedia->setMediaType($fileExt[1]);
                $newMedia->setIsBanner(null);
                move_uploaded_file($tmp, "$dirImages/$filename");
                $trick->addMedia($newMedia);
                $mediaRepository->getEntityManager()->persist($newMedia);
            }
        }
    }

    /**
     * Summary of saveVideos
     *
     * @param Media           $media           Object
     * @param Trick           $trick           Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return void
     *
     * @throws \Exception
     */
    public function saveVideos(
        Media $media, Trick $trick, MediaRepository $mediaRepository
    ): void {
        $videos = $media->getVideos();
        $dirVideos = "../public/assets/videos";

        if (!empty($media->getVideos())) {
            foreach ($videos as $video) {
                $newMedia = new Media();
                $fileExt = explode('.', $video->getClientOriginalName());
                $filename = str_replace(
                        "/",
                        "",
                        base64_encode(random_bytes(9))
                    ) . '.' . $fileExt[1];
                $videoPath = "/assets/videos/$filename";
                $tmp = $video->getPathname();
                $newMedia->setMediaPath($videoPath);
                $newMedia->setMediaType($fileExt[1]);
                $newMedia->setIsBanner();
                move_uploaded_file($tmp, "$dirVideos/$filename");
                $trick->addMedia($newMedia);
                $mediaRepository->getEntityManager()->persist($newMedia);
            }
        }
    }
}

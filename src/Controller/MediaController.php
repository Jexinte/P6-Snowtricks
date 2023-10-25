<?php
/**
 * Handle medias
 *
 * PHP version 8
 *
 * @category Controller
 * @package  MediaController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Controller;

use App\Entity\Media;
use App\Form\Type\UpdateBannerFile;
use App\Form\Type\UpdateEmbedUrl;
use App\Form\Type\UpdateFile;
use App\Repository\MediaRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handle medias
 *
 * PHP version 8
 *
 * @category Controller
 * @package  MediaController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class MediaController extends AbstractController
{
    /**
     * Summary of updateTrickMediaPage
     *
     * @param Media $media Object
     *
     * @return Response
     */
    #[Route('/update-trick-media/{id}', name: 'update_trick_media_page', methods: ["GET"])]
    public function updateTrickMediaPage(Media $media): Response
    {
        $userConnected = $this->getUser() ?: '';

        $form = $this->checkTypeFormSent($media);

        $parameters["user_connected"] = $userConnected;
        $parameters["form"] = $form;
        $parameters["media"] = $media;
        return new Response($this->render("update_media.twig", $parameters));
    }

    /**
     * Summary of updateTrickMediaValidator
     *
     * @param Media           $media           Object
     * @param Request         $request         Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return Response
     */
    #[Route('/update-trick-media/{id},', name: 'update_trick_media_form', methods: ["PUT"])]
    public function updateTrickMediaValidator(
        Media $media,
        Request $request,
        MediaRepository $mediaRepository
    ): Response {
        $form = $this->checkTypeFormSent($media);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            switch (true) {
            case !empty($form->getData()->getUpdatedFile()):
                $media->setUpdatedFile($form->getData()->getUpdatedFile());
                $this->updateTrickFile($media, $mediaRepository);
                $this->addFlash(
                    "success",
                    "Votre fichier a bien été mis à jour !"
                );
                return $this->redirectToRoute('homepage');

            case !empty($form->getData()->getUpdatedBannerFile()):
                $media->setUpdatedBannerFile(
                    $form->getData()->getUpdatedBannerFile()
                );
                $this->updateTrickFile($media, $mediaRepository);
                $this->addFlash(
                    "success",
                    "Votre fichier a bien été mis à jour !"
                );
                return $this->redirectToRoute('homepage');
            case !empty($form->getData()->getEmbedUrlUpdated()):

                preg_match(
                    '/<iframe[^>]+src="([^"]+)"/i',
                    $form->getData()->getEmbedUrlUpdated(),
                    $matches
                );
                $urlCleaned = $matches[1];
                $media->setEmbedUrlUpdated($urlCleaned);

                $this->updateEmbedUrl($media, $mediaRepository);
                $this->addFlash(
                    "success",
                    "Votre lien vidéo a bien été mis à jour !"
                );
                return $this->redirectToRoute('homepage');
            default:
                $this->addFlash(
                    "success",
                    "Votre média a bien été mis à jour !"
                );
                return $this->redirectToRoute('homepage');
            }
        }


        $parameters["media"] = $media;
        $parameters["form"] = $form;
        return new Response(
            $this->render("update_media.twig", $parameters), 400
        );
    }

    /**
     * Summary of checkTypeFormSent
     *
     * @param Media $media Object
     *
     * @return FormInterface|null
     */
    public function checkTypeFormSent(Media $media): ?FormInterface
    {
        $imgExtensions = array('jpg', 'png', 'webp', 'mp4');
        if ($media->getIsBanner()) {
            return $this->createForm(UpdateBannerFile::class);
        } elseif ($media->getMediaType() == "web") {
            return $this->createForm(
                UpdateEmbedUrl::class
            );
        } elseif (in_array($media->getMediaType(), $imgExtensions)) {
            return $this->createForm(
                UpdateFile::class
            );
        }
        return null;
    }


    /**
     * Summary of updateTrickFile
     *
     * @param Media           $media           Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function updateTrickFile(
        Media $media, MediaRepository $mediaRepository
    ): bool {
        $dir = "";
        $filePath = "";
        $tmp = "";
        $fileExt = "";
        if ($media->getIsBanner()) {
            $fileExt = explode(
                '.',
                $media->getUpdatedBannerFile()->getClientOriginalName()
            );
            $filename = str_replace(
                "/",
                "",
                base64_encode(random_bytes(9))
            ) . '.' . $fileExt[1];
            $dir = "../public/assets/img/banner";
            $filePath = "/assets/img/banner/$filename";
            $tmp = $media->getUpdatedBannerFile()->getPathname();
        } else {
            $fileExt = explode(
                '.',
                $media->getUpdatedFile()->getClientOriginalName()
            );
            $filename = str_replace(
                "/",
                "",
                base64_encode(random_bytes(9))
            ) . '.' . $fileExt[1];
            if ($fileExt[1] == "mp4") {
                $dir = "../public/assets/videos";
                $filePath = "/assets/videos/$filename";
            } else {
                $dir = "../public/assets/img";
                $filePath = "/assets/img/$filename";
            }
            $tmp = $media->getUpdatedFile()->getPathname();
        }

        unlink("../public" . $media->getMediaPath());


        $media->setMediaPath($filePath);
        $media->setMediaType($fileExt[1]);
        $mediaRepository->getEntityManager()->flush();


        move_uploaded_file($tmp, "$dir/$filename");
        return true;
    }

    /**
     * Summary of updateEmbedUrl
     *
     * @param Media           $media           Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return bool
     */
    public function updateEmbedUrl(
        Media $media, MediaRepository $mediaRepository
    ): bool {
        $embedUrl = $media->getEmbedUrlUpdated();
        $media->setMediaPath($embedUrl);
        $media->setMediaType("web");
        $mediaRepository->getEntityManager()->flush();
        return true;
    }

    /**
     * Summary of deleteTrickMedia
     *
     * @param Media           $media           Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return Response
     */
    #[Route('/delete-trick-media/{id}', name: 'delete_trick_media', methods: ["GET"])]
    public function deleteTrickMedia(
        Media $media, MediaRepository $mediaRepository
    ): Response {
        if ($media->getMediaType() != "web") {
            unlink("../public" . $media->getMediaPath());
        }
        $mediaRepository->getEntityManager()->remove($media);
        $mediaRepository->getEntityManager()->flush();
        $this->addFlash(
            "success",
            "La suppression de votre média a bien été prise en compte !"
        );
        return $this->redirectToRoute('homepage');
    }
}

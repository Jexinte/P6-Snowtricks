<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\Type\UpdateEmbedUrl;
use App\Form\Type\UpdateFile;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{


    #[Route('/update-trick-media/{id}', name: 'update_trick_media_page', methods: ["GET"])]
    public function updateTrickMediaPage(Media $media): Response
    {
        $userConnected = !is_null($this->getUser()) ? current($this->getUser()->getRoles()) : '';

        $form = $media->getMediaType() == "web" ? $this->createForm(UpdateEmbedUrl::class) : $this->createForm(
            UpdateFile::class
        );

        $parameters["user_connected"] = $userConnected;
        $parameters["form"] = $form;
        $parameters["media"] = $media;
        return new Response($this->render("update_media.twig", $parameters));
    }

    #[Route('/update-trick-media/{id},', name: 'update_trick_media_form', methods: ["PUT"])]
    public function updateTrickMediaValidator(
        Media $media,
        Request $request,
        MediaRepository $mediaRepository
    ): Response {
        $form = $media->getMediaType() == "web" ? $this->createForm(UpdateEmbedUrl::class) : $this->createForm(
            UpdateFile::class
        );

        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $file = !empty($form->getData()->getUpdatedFile()) ? $form->getData()->getUpdatedFile() : '';
            $embedUrl = !empty($form->getData()->getEmbedUrl()) ? $form->getData()->getEmbedUrl() : '';
            switch (true) {
                case !empty($file):
                    $media->setUpdatedFile($file);
                    $this->updateTrickFile($media, $mediaRepository);
                    $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                    return $this->redirectToRoute('homepage');
                case !empty($embedUrl):
                    preg_match('/<iframe[^>]+src="([^"]+)"/i', $embedUrl, $matches);
                    $urlCleaned = $matches[1];
                    $media->setEmbedUrl($urlCleaned);
                    $this->updateEmbedUrl($media, $mediaRepository);
                    $this->addFlash("success", "Le lien a bien été mis à jour !");
                    return $this->redirectToRoute('homepage');
                default:
                    $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                    return $this->redirectToRoute('homepage');
            }
        }


        $parameters["media"] = $media;
        $parameters["form"] = $form;
        return new Response($this->render("update_media.twig", $parameters), 400);
    }

    public function updateTrickFile(Media $media, MediaRepository $mediaRepository): bool
    {
        $fileExt = explode('.', $media->getUpdatedFile()->getClientOriginalName());
        $filePathInDb = current($mediaRepository->findBy(["id" => $media->getId()]));
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


        $media->setMediaPath($filePath);
        $media->setMediaType($fileExt[1]);
        $mediaRepository->getEntityManager()->flush();

        $tmp = $media->getUpdatedFile()->getPathname();
        move_uploaded_file($tmp, "$dir/$filename");
        return true;
    }

    public function updateEmbedUrl(Media $media, MediaRepository $mediaRepository): bool
    {
        $embedUrl = $media->getEmbedUrl();
        $media->setMediaPath($embedUrl);
        $media->setMediaType("web");
        $mediaRepository->getEntityManager()->flush();
        return true;
    }

    #[Route('/delete-trick-media/{id}', name: 'delete_trick_media', methods: ["GET"])]
    public function deleteTrickMedia(Media $media, MediaRepository $mediaRepository): Response
    {
        if ($media->getMediaType() != "web") {
            unlink("../public" . $media->getMediaPath());
        }
        $mediaRepository->getEntityManager()->remove($media);
        $mediaRepository->getEntityManager()->flush();
        $this->addFlash("success", "La suppression du média a bien été prise en compte !");
        return $this->redirectToRoute('homepage');
    }
}

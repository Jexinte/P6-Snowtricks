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
    public function updateTrickMediaPage(Media $media, MediaRepository $mediaRepository, Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        if(!$userConnected)
        {
            return  $this->redirectToRoute('forbidden');
        }
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
            $mediaEntity = $form->getData();
            $file = !empty($mediaEntity->getUpdatedFile()) ? $mediaEntity->getUpdatedFile() : '';
            $embedUrl = !empty($mediaEntity->getEmbedUrl()) ? $mediaEntity->getEmbedUrl() : '';
            switch (true) {
                case !empty($file):
                    $mediaEntity->setUpdatedFile($file);
                    $fileUpdated = $mediaRepository->updateTrickMedia($media->getId(), $mediaEntity);
                    if ($fileUpdated) {
                        $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                        return $this->redirectToRoute('homepage');
                    }
                    break;
                case !empty($embedUrl):
                    $mediaEntity->setEmbedUrl($embedUrl);
                    preg_match('/<iframe[^>]+src="([^"]+)"/i', $mediaEntity->getEmbedUrl(), $matches);
                    $urlCleaned = $matches[1];
                    $mediaEntity->setEmbedUrl($urlCleaned);
                    $urlUpdated = $mediaRepository->updateTrickMedia($media->getId(), $mediaEntity);

                    if ($urlUpdated) {
                        $this->addFlash("success", "Le lien a bien été mis à jour !");
                        return $this->redirectToRoute('homepage');
                    }
                    break;
                default:
                    $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                    return $this->redirectToRoute('homepage');
            }
        }


        $parameters["media"] = $media;
        $parameters["form"] = $form;
        return new Response($this->render("update_media.twig", $parameters), 400);
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

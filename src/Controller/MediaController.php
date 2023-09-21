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
    public function updateTrickMediaPage(int $id, MediaRepository $mediaRepository, Request $request): Response
    {
        $media = current($mediaRepository->findBy(["id" => $id]));
        $form = $media->getMediaType() == "web" ? $this->createForm(UpdateEmbedUrl::class) : $this->createForm(
            UpdateFile::class
        );
        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["form"] = $form;
        $parameters["media"] = $media;
        return new Response($this->render("update_media.twig", $parameters));
    }

    #[Route('/update-trick-media/{id},', name: 'update_trick_media_form', methods: ["PUT"])]
    public function updateTrickMediaValidator(
        int $id,
        Request $request,
        MediaRepository $mediaRepository
    ): Response {
        $media = current($mediaRepository->findBy(["id" => $id]));
        $form = $media->getMediaType() == "web" ? $this->createForm(UpdateEmbedUrl::class) : $this->createForm(
            UpdateFile::class
        );
        $token = array_key_exists("update_file", $request->request->all()) ? $request->request->all(
        )["update_file"]["_token"] : $request->request->all()["update_embed_url"]["_token"];
        $form->handleRequest($request);
        $mediaEntity = new Media();

        if ($form->isValid() && $form->isSubmitted() && $this->isCsrfTokenValid("update_media", $token)) {
            $file = !empty($form->getData()->getUpdatedFile()) ? $form->getData()->getUpdatedFile() : '';
            $embedUrl = !empty($form->getData()->getEmbedUrl()) ? $form->getData()->getEmbedUrl() : '';
            switch (true) {
                case !empty($file):
                    $mediaEntity->setUpdatedFile($file);
                    $fileUpdated = $mediaRepository->updateTrickMedia($id, $mediaEntity);
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
                    $urlUpdated = $mediaRepository->updateTrickMedia($id, $mediaEntity);

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
    public function deleteTrickMedia(int $id, MediaRepository $mediaRepository): Response
    {
        $media = current($mediaRepository->findBy(["id" => $id]));
        if ($media->getMediaType() != "web") {
            unlink("../public" . $media->getMediaPath());
        }
        $mediaRepository->getEntityManager()->remove($media);
        $mediaRepository->getEntityManager()->flush();
        $this->addFlash("success", "La suppression du média a bien été prise en compte !");
        return $this->redirectToRoute('homepage');
    }
}

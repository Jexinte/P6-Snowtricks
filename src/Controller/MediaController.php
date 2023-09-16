<?php

namespace App\Controller;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;

class MediaController extends AbstractController
{


    public function initializeUpdateFileForm(): FormBuilderInterface
    {
        return $this->createFormBuilder()->add("updatedFile", FileType::class, options: [
            'label' => 'Sélectionner un fichier',
            'required' => false,
            'constraints' => [
                new File(
                    maxSize: '3000K',
                    extensions: ['jpg', 'png', 'webp', 'mp4'],
                    extensionsMessage: 'Seuls les fichiers ayant pour extensions : jpg , png ,webp et mp4 sont acceptés !',
                ),
            ]
        ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('PUT');
    }

    public function initializeUpdateEmbedUrlForm(): FormBuilderInterface
    {
        return $this->createFormBuilder()->add("embedUrl", TextType::class, options: [
            'label' => 'Url Vidéo Dailymotion / Youtube',
            'required' => false,
            'constraints' => [
                new Regex(
                    pattern: '/<iframe[^>]+src="([^"]+)"/i',
                    message: "Oops ! Il semblerait que le format de votre url n'est pas bon, merci de vérifier ce qu'il en est",
                    match: true,
                ),
            ]
        ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']])
            ->setMethod('PUT');
    }

    #[Route('/update-trick-media/{id}', name: 'update_trick_media_page', methods: ["GET"])]
    public function updateTrickMediaPage(int $id, MediaRepository $mediaRepository): Response
    {
        $media = current($mediaRepository->findBy(["id" => $id]));
        if ($media->getMediaType() == "web") {
            $form = $this->initializeUpdateEmbedUrlForm()->getForm();
        } else {
            $form = $this->initializeUpdateFileForm()->getForm();
        }
        $template = "update_media.twig";
        $parameters["form"] = $form;
        $parameters["media"] = $media;
        return new Response($this->render($template, $parameters));
    }

    #[Route('/update-trick-media/{id},', name: 'update_trick_media_form', methods: ["PUT"])]
    public function updateTrickMediaValidator(
        int $id,
        Request $request,
        MediaRepository $mediaRepository
    ): Response {
        $media = current($mediaRepository->findBy(["id" => $id]));
        $formBuilder = $media->getMediaType() == "web" ? $this->initializeUpdateEmbedUrlForm(
        ) : $this->initializeUpdateFileForm();
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        $mediaEntity = new Media();
        if ($form->isValid() && $form->isSubmitted()) {
            $token = $request->request->all()["form"]["_token"];
            if ($this->isCsrfTokenValid("form", $token)) {
                $file = !empty($form->getData()["updatedFile"]) ? $form->getData()["updatedFile"] : '';
                $embedUrl = !empty($form->getData()["embedUrl"]) ? $form->getData()["embedUrl"] : '';
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
                            $this->addFlash("success", "Votre url a bien été mis à jour !");
                            return $this->redirectToRoute('homepage');
                        }
                        break;
                    default:
                        $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                        return $this->redirectToRoute('homepage');
                }
            }
        }
        $template = "update_media.twig";


        $parameters["media"] = $media;
        $parameters["form"] = $form;
        return new Response($this->render($template, $parameters), 400);
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

<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\Type\AddComment;
use App\Form\Type\CreateTrick;
use App\Form\Type\UpdateEmbedUrl;
use App\Form\Type\UpdateTrickContent;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;


class TrickController extends AbstractController
{


    #[Route('/{trickname}/details/{id}', name: 'trick', methods: ["GET"])]
    public function getTrickPage(
        string $trickname,
        int $id,
        TrickRepository $trickRepository,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        MediaRepository $mediaRepository,
        IntlDateFormatter $dateFormatter,
        Request $request
    ): Response {
        $form = $this->createForm(AddComment::class);
        $userConnected = $request->getSession()->get('user_connected');
        $trick = current($trickRepository->findBy(["id" => $id]));
        if (!$trick) {
            throw $this->createNotFoundException();
        }
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
        unset($medias[0]);
        $mainBannerOfTrick = current($mediaRepository->findBy(["idTrick" => $id, "isBanner" => true]));
        $trickComments = $commentRepository->getComments($id, $userRepository);
        if ($request->query->get('page') !== null && !empty($request->query->get('page'))) {
            $currentPage = $request->query->get('page');
        } else {
            $currentPage = 1;
        }
        $nbComments = count($trickComments);
        $commentsPerPage = 10;
        $pages = ceil($nbComments / $commentsPerPage);
        $firstPage = ($currentPage * $commentsPerPage) - $commentsPerPage;
        $commentsPerPageRequest = $commentRepository->getCommentsPerPage($id, $firstPage, $commentsPerPage);
        foreach ($commentsPerPageRequest as $comment) {
            $comment->date = ucfirst($dateFormatter->format($comment->getDateCreation()));
        }
        $parameters["comments"] = $commentsPerPageRequest;
        $parameters["pages"] = $pages;
        $parameters["currentPage"] = $currentPage;
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $dateTrick = ucfirst($dateFormatter->format($trick->getDate()));
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        $parameters["totalComments"] = $nbComments;
        $parameters["banner"] = $mainBannerOfTrick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $dateTrick;
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';

        return new Response($this->render("trick.twig", $parameters));
    }


    #[Route('/create-trick', name: 'create_trick_get', methods: ["GET"])]
    public function createTrickPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $form = $this->createForm(CreateTrick::class);
        $parameters["user_ connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["form"] = $form;
        return new Response($this->render("create_trick.twig", $parameters));
    }


    #[Route('/create-trick', name: 'create_trick_post', methods: ["POST"])]
    public function createTrick(
        Request $request,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        DateTime $dateTime
    ): Response {
        $userConnected = $request->getSession()->get('user_connected');
        $form = $this->createForm(CreateTrick::class);
        $form->handleRequest($request);
        $token = $request->request->all()["create_trick"]['token'];
        if ($form->isValid() && $form->isSubmitted() && $this->isCsrfTokenValid('create_trick', $token)) {
            $formData = $form->getData();
            $trickEntity = new Trick();
            $trickEntity->setName($formData->getName());
            $trickEntity->setDescription($formData->getDescription());
            $trickEntity->setTrickGroup($formData->getTrickGroup());
            $trickEntity->setDate($dateTime);
            $trickEntity->isTrickUpdated(false);
            $mediaEntity = new Media();
            $mediaEntity->setImages($formData->getImages());
            $mediaEntity->setBannerFile($formData->getBannerFile());
            $mediaEntity->setVideos($formData->getVideos());
            $mediaEntity->setEmbedUrl($formData->getEmbedUrl());

            if (!empty($mediaEntity->getEmbedUrl())) {
                preg_match('/<iframe[^>]+src="([^"]+)"/i', $mediaEntity->getEmbedUrl(), $matches);
                $urlCleaned = $matches[1];
                $mediaEntity->setEmbedUrl($urlCleaned);
            }


            $trickRepository->getEntityManager()->persist($trickEntity);
            $trickRepository->getEntityManager()->flush();
            $tricks = $trickRepository->findAll();
            $trickSaved = end($tricks);
            $mediaEntity->setIdTrick($trickSaved->getId());
            $mediaRepository->saveTrickMedias($mediaEntity);
            return $this->redirectToRoute('homepage');
        }
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["form"] = $form;
        return new Response($this->render("create_trick.twig", $parameters), 400);
    }




    #[Route('/update-trick/{trickname}/{id}', name: 'update_trick_get', methods: ["GET"])]
    public function updateTrickPage(
        int $id,
        string $trickname,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        Request $request,
        IntlDateFormatter $dateFormatter
    ): Response {
        $userConnected = $request->getSession()->get('user_connected');
        $trick = current($trickRepository->findBy(["id" => $id]));
        $form = $this->createForm(UpdateTrickContent::class, $trick);
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
        unset($medias[0]);
        $mainBannerOfTrick = current($mediaRepository->findBy(["idTrick" => $id, "isBanner" => true]));
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $dateTrick = $trick->getDate();
        $date = $dateFormatter->format($dateTrick);
        $parameters["trick"] = $trick;
        $parameters["banner"] = $mainBannerOfTrick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $date;
        $parameters["form"] = $form;
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render("update_trick.twig", $parameters));
    }


    #[Route('/update-trick-content/{trickname}/{id}', name: 'update_trick_content_put', methods: ["PUT"])]
    public function updateTrickContentValidator(
        int $id,
        string $trickname,
        Request $request,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response {
        $trick = current($trickRepository->findBy(["id" => $id]));
        $media = $mediaRepository->findBy(["idTrick" => $id]);
        $form = $this->createForm(UpdateTrickContent::class, $trick);
        $form->handleRequest($request);
        $trickEntity = new Trick();
        $token = $request->request->all()["update_trick_content"]["_token"];

        if ($form->isValid() && $form->isSubmitted() && $this->isCsrfTokenValid('update_trick_content', $token)) {
            $trickEntity->setNameUpdated($form->getData()->getName());
            $trickEntity->setDescription($form->getData()->getDescription());
            $trickEntity->setTrickGroup($form->getData()->getTrickGroup());
            $trickEntity->isTrickUpdated(true);
            $trickRepository->updateTrick($id, $trickEntity);
            $this->addFlash("success", "Le trick a bien été mis à jour !");
            return $this->redirectToRoute('homepage');
        }

        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["medias"] = $media;
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        return new Response($this->render("update_trick.twig", $parameters), 400);
    }

    #[Route('/trick/delete/{trickname}/{id}', name: 'delete_trick', methods: ["DELETE"])]
    public function deleteTrick(
        ?int $id,
        string $trickname,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response|RedirectResponse {
        if (is_null($id)) {
            throw $this->createNotFoundException();
        }

        $trick = $trickRepository->find(["id" => $id]);
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
        foreach ($medias as $media) {
            if ($media->getMediaType() != "web") {
                unlink("../public" . $media->getMediaPath());
            }
            $mediaRepository->getEntityManager()->remove($media);
        }
        $mediaRepository->getEntityManager()->flush();
        $trickRepository->getEntityManager()->remove($trick);
        $trickRepository->getEntityManager()->flush();

        $this->addFlash("success", "La suppression du trick a bien été pris en compte !");
        return $this->redirectToRoute('homepage');
    }


}

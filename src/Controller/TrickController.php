<?php

namespace App\Controller;

use App\Entity\Media;
use App\Enumeration\CodeStatus;
use App\Form\Type\AddComment;
use App\Form\Type\CreateTrick;
use App\Form\Type\CreateTrickMedia;
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


    #[Route('/{slug}/details/{id}', name: 'trick', methods: ["GET"])]
    public function getTrickPage(
        Trick $trick,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        MediaRepository $mediaRepository,
        IntlDateFormatter $dateFormatter,
        Request $request
    ): Response {
        $userConnected = $request->getSession()->get('user_connected');
        $id = $trick->getId();
        $form = $this->createForm(AddComment::class);
        $medias = $mediaRepository->findBy(["idTrick" => $id, "isBanner" => null]);
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
            $comment->date = ucfirst($dateFormatter->format($comment->getCreatedAt()));
        }
        $parameters["comments"] = $commentsPerPageRequest;
        $parameters["pages"] = $pages;
        $parameters["currentPage"] = $currentPage;
        $dateTrick = ucfirst($dateFormatter->format($trick->getCreatedAt()));
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        $parameters["totalComments"] = $nbComments;
        $parameters["banner"] = $mainBannerOfTrick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $dateTrick;
        $parameters["user_connected"] = $userConnected;

        return new Response($this->render("trick.twig", $parameters));
    }


    #[Route('/create-trick', name: 'create_trick_get', methods: ["GET"])]
    public function createTrickPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');

        $form = $this->createForm(CreateTrick::class);
        $form->add('mediaForm', CreateTrickMedia::class);
        $parameters["user_connected"] = $userConnected;
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
        $form->add('mediaForm', CreateTrickMedia::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trickEntity = $form->getData();
            $trickEntity->setCreatedAt($dateTime);
            $mediaEntity = $form->get('mediaForm')->getData();
            if (!empty($mediaEntity->getEmbedUrl())) {
                preg_match('/<iframe[^>]+src="([^"]+)"/i', $mediaEntity->getEmbedUrl(), $matches);
                $urlCleaned = $matches[1];
                $mediaEntity->setEmbedUrl($urlCleaned);
            }


            $mediaRepository->saveTrickMedias($mediaEntity, $trickEntity);
            $trickRepository->getEntityManager()->persist($trickEntity);
            $trickRepository->getEntityManager()->flush();
            $this->addFlash("success", "Votre nouveau trick a été créé avec succès !");
            return $this->redirectToRoute('homepage');
        }

        $parameters["user_connected"] = $userConnected;
        $parameters["form"] = $form;
        return new Response($this->render("create_trick.twig", $parameters), CodeStatus::CLIENT);
    }


    #[Route('/update-trick/{slug}/details/{id}', name: 'update_trick_get', methods: ["GET"])]
    public function updateTrickPage(
        Trick $trick,
        MediaRepository $mediaRepository,
        Request $request,
        IntlDateFormatter $dateFormatter
    ): Response {
        $userConnected = $request->getSession()->get('user_connected');
        if (!$userConnected) {
            return $this->redirectToRoute('forbidden');
        }
        $form = $this->createForm(UpdateTrickContent::class, $trick);
        $medias = $mediaRepository->findBy(["idTrick" => $trick->getId(), "isBanner" => null]);
        $mainBannerOfTrick = current($mediaRepository->findBy(["idTrick" => $trick->getId(), "isBanner" => true]));
        $dateTrick = $trick->getCreatedAt();
        $date = $dateFormatter->format($dateTrick);
        $parameters["trick"] = $trick;
        $parameters["banner"] = $mainBannerOfTrick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $date;
        $parameters["form"] = $form;
        $parameters["user_connected"] = $userConnected;
        return new Response($this->render("update_trick.twig", $parameters));
    }


    #[Route('/update-trick-content/{slug}/details/{id}', name: 'update_trick_content_put', methods: ["PUT"])]
    public function updateTrickContentValidator(
        Trick $trick,
        Request $request,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        DateTime $dateTime
    ): Response {
        $media = $mediaRepository->findBy(["idTrick" => $trick->getId(),"isBanner" => null]);
        $mainBannerOfTrick = current($mediaRepository->findBy(["idTrick" => $trick->getId(), "isBanner" => true]));
        $form = $this->createForm(UpdateTrickContent::class, $trick);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $trickEntity = $form->getData();
            $trickEntity->setUpdatedAt($dateTime);
            $trickRepository->getEntityManager()->flush();
            $this->addFlash("success", "Votre trick a été mis à jour avec succès !");
            return $this->redirectToRoute('homepage');
        }

        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = $userConnected;
        $parameters["medias"] = $media;
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        $parameters["banner"] = $mainBannerOfTrick;
        return new Response($this->render("update_trick.twig", $parameters), 400);
    }

    #[Route('/trick/delete/{slug}/{id}', name: 'delete_trick', methods: ["DELETE"])]
    public function deleteTrick(
        Trick $trick,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response|RedirectResponse {
        if (is_null($trick->getId())) {
            throw $this->createNotFoundException();
        }

        $medias = $mediaRepository->findBy(["idTrick" => $trick->getId()]);

        foreach ($medias as $media) {
            if ($media->getMediaType() != "web") {
                unlink("../public" . $media->getMediaPath());
            }
        }
        $trickRepository->getEntityManager()->remove($trick);
        $trickRepository->getEntityManager()->flush();

        $this->addFlash("success", "Votre trick a été supprimé avec succès !");
        return $this->redirectToRoute('homepage');
    }


}

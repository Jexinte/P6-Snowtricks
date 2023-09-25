<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\Type\AddComment;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use IntlDateFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommentController extends AbstractController
{







    #[Route('/add-comment/{id}', name: 'add_comment', methods: ["POST"])]
    public function handleAddComment(
        int $id,
        Request $request,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        MediaRepository $mediaRepository,
        IntlDateFormatter $dateFormatter,
        SluggerInterface $slugger,
        DateTime $dateTime,
        TrickRepository $trickRepository,
    ): Response {
        $commentEntity = new Comment();
        $form = $this->createForm(AddComment::class, $commentEntity);
        $form->handleRequest($request);
        $user = current($userRepository->findBy(["id" => $request->getSession()->get('user_id')]));
        $trick = current($trickRepository->findBy(["id" => $id]));
        $token = $request->request->all()["add_comment"]["_token"];
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isCsrfTokenValid("add_comment", $token)) {
                $formData = $form->getData();
                $commentEntity->setContent($formData->getContent());
                $commentEntity->setIdUser($user->getId());
                $commentEntity->setIdTrick($id);
                $commentEntity->setUserProfileImage($user->getProfileImage());
                $commentEntity->setDateCreation($dateTime);
                $trick->addComment($commentEntity);
                $commentRepository->getEntityManager()->flush();
                $trickNameSlug = $slugger->slug($trick->getName())->lower();
                return $this->redirectToRoute("trick", [
                    "trickname" => $trickNameSlug,
                    "id" => $id
                ]);
            }
        }
        $dateTrick = ucfirst($dateFormatter->format($trick->getDate()));
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
        $trickComments = $commentRepository->getComments($id, $userRepository);
        $parameters["trick"] = $trick;
        $parameters["trick_date"] = $dateTrick;
        $parameters["medias"] = $medias;
        $parameters["user_connected"] = $request->getSession()->get('user_connected');


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
        $parameters["comments"] = $commentsPerPageRequest;
        $parameters["pages"] = $pages;
        $parameters["currentPage"] = $currentPage;
        $parameters["form"] = $form;
        return new Response($this->render("trick.twig", $parameters), 400);
    }


}

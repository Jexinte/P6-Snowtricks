<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
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
//        int $id,
        Request $request,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        MediaRepository $mediaRepository,
        IntlDateFormatter $dateFormatter,
        SluggerInterface $slugger,
        DateTime $dateTime,
        Trick $trick,
    ): Response {
        $form = $this->createForm(AddComment::class);
        $form->handleRequest($request);
        $user = current($userRepository->findBy(["id" => $request->getSession()->get('user_id')]));
        $medias = $mediaRepository->findBy(["idTrick" => $trick->getId()]);
        $mainBannerOfTrick = current($mediaRepository->findBy(["idTrick" => $trick->getId(), "isBanner" => true]));
        if ($form->isSubmitted() && $form->isValid()) {
            $commentEntity = $form->getData();
            $commentEntity->setIdUser($user->getId());
            $commentEntity->setIdTrick($trick->getId());
            $commentEntity->setUserProfileImage($user->getProfileImage());
            $commentEntity->setCreatedAt($dateTime);
            $trick->addComment($commentEntity);
            $user->addComment($commentEntity);
            $commentRepository->getEntityManager()->flush();
            $trickNameSlug = $slugger->slug($trick->getName())->lower();
            return $this->redirectToRoute("trick", [
                "trickname" => $trickNameSlug,
                "id" => $trick->getId()
            ]);
        }
        $dateTrick = is_null($trick->getUpdatedAt()) ? ucfirst($dateFormatter->format($trick->getCreatedAt())) : ucfirst($dateFormatter->format($trick->getUpdatedAt()));

        $trickComments = $commentRepository->getComments($trick->getId(), $userRepository);


        if ($request->query->get('page') !== null && !empty($request->query->get('page'))) {
            $currentPage = $request->query->get('page');
        } else {
            $currentPage = 1;
        }
        $nbComments = count($trickComments);
        $commentsPerPage = 10;
        $pages = ceil($nbComments / $commentsPerPage);
        $firstPage = ($currentPage * $commentsPerPage) - $commentsPerPage;
        $commentsPerPageRequest = $commentRepository->getCommentsPerPage($trick->getId(), $firstPage, $commentsPerPage);
        foreach ($commentsPerPageRequest as $comment) {
            $comment->date = ucfirst($dateFormatter->format($comment->getCreatedAt()));
        }

        $parameters["comments"] = $commentsPerPageRequest;
        $parameters["pages"] = $pages;
        $parameters["currentPage"] = $currentPage;
        $parameters["form"] = $form;
        $parameters["totalComments"] = $nbComments;
        $parameters["trick"] = $trick;
        $parameters["trick_date"] = $dateTrick;
        $parameters["medias"] = $medias;
        $parameters["banner"] = $mainBannerOfTrick;
        $parameters["user_connected"] = $request->getSession()->get('user_connected');

        return new Response($this->render("trick.twig", $parameters), 400);
    }


}

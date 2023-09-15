<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use IntlDateFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTime;

class CommentController extends AbstractController
{

    private IntlDateFormatter $dateFormatter;
    private AsciiSlugger $slugger;
    private DateTime $actualDate;

    public function __construct()
    {
        $this->dateFormatter = new IntlDateFormatter('fr_Fr', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $this->slugger = new AsciiSlugger();
        $this->actualDate = new DateTime();
    }
    #[Route('/add-comment/{id}',name:'add_comment', methods: ["POST"])]
    public function handleAddComment(
        int $id,
        Request $request,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        ValidatorInterface $validator,
        MediaRepository $mediaRepository,
        TrickRepository $trickRepository,
    ): Response {
        $template = "trick.twig";
        $trick = current($trickRepository->findBy(["id" => $id ]));
        $dateTrick = $this->dateFormatter->format($trick->getDate());
        $user = current($userRepository->findBy(["id" => $request->getSession()->get('user_id')]));
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
        $trickComments = $commentRepository->getComments($id, $userRepository);
        $parameters["trick"] = $trick;
        $parameters["trick_date"] = $dateTrick;
        $parameters["medias"] = $medias;
        $parameters["user_connected"] = $request->getSession()->get('user_connected');
        $token = $request->request->get('token');
        $comment = new Comment();
        $numberOfErrors = 0;
        $groups = [
            "content_exception",
            "content_wrong_format_exception"
        ];

        $groupsViolations = [];
        $comment->setContent($request->request->get('comment'));
        $comment->setIdUser($user->getId());
        $comment->setIdTrick($id);
        $comment->setUserProfileImage($user->getProfileImage());
        $comment->setDateCreation($this->actualDate);
        foreach ($groups as $group) {
            $errors = $validator->validate($comment, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }
        if ($numberOfErrors == 0 && $this->isCsrfTokenValid("add_comment",$token)) {
            $commentRepository->saveComment($comment);
            $trickNameSlug = $this->slugger->slug($trick->getName())->lower();

            return $this->redirectToRoute("trick",["trickname" => $trickNameSlug,
                "id" => $id]);
        }

        if($request->query->get('page') !== null && !empty($request->query->get('page'))){
            $currentPage = $request->query->get('page');
        } else{
            $currentPage = 1;
        }
        $nbComments = count($trickComments);
        $commentsPerPage = 10;
        $pages = ceil($nbComments/$commentsPerPage);
        $firstPage = ($currentPage * $commentsPerPage) - $commentsPerPage;
        $commentsPerPageRequest = $commentRepository->getCommentsPerPage($firstPage,$commentsPerPage);
        $parameters["comments"] = $commentsPerPageRequest;
        $parameters["pages"] = $pages;
        $parameters["currentPage"] = $currentPage;
        $parameters["exceptions"] = $groupsViolations;
        return new Response($this->render($template, $parameters), 400);
    }



}

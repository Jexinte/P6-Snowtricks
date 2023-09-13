<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use DateTime;

class CommentController extends AbstractController
{
    public string $template = "";
    /**
     * @var array <string,int>
     */
    public array $parameters = [];


    #[Route('/comment', methods: ["POST"])]
    public function handleSendComment(
        Request $request,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        ValidatorInterface $validator,
        MediaRepository $mediaRepository
    ): Response {
        $this->template = "trick.twig";
        $this->parameters["trick"] = $request->getSession()->get('trick');
        $this->parameters["trick_date"] = $request->getSession()->get('trick_date');
        $this->parameters["medias"] = $mediaRepository->getTrickMedia($request->getSession()->get('trick')->getId());
        $this->parameters["user_connected"] = $request->getSession()->get('user_connected');
        $cookie = $this->getToken($request);
        $tokenInSession = $request->getSession()->get('token');
        $comment = new Comment();
        $actualDate = new DateTime();
        $slug = new AsciiSlugger();
        $numberOfErrors = 0;
        $groups = [
            "content_exception",
            "content_wrong_format_exception"
        ];

        $groupsViolations = [];
        $comment->setContent($request->request->get('comment'));
        $comment->setIdUser($request->getSession()->get('user_id'));
        $comment->setIdTrick($request->getSession()->get('trick')->getId());
        $comment->setUserProfileImage($request->getSession()->get("profile_image"));
        $comment->setDateCreation($actualDate);
        foreach ($groups as $group) {
            $errors = $validator->validate($comment, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }
        if ($numberOfErrors == 0 && $cookie == $tokenInSession) {
            $commentRepository->saveComment($comment);
            $trickNameSlug = $slug->slug($request->getSession()->get('trick')->getName())->lower();

            return $this->redirectToRoute("trick",["trickname" => $trickNameSlug,
                "id" => $request->getSession()->get('trick')->getId()]);
        }
        $this->parameters["exceptions"] = $groupsViolations;
        $this->parameters["comments"] = $commentRepository->getComments($comment->getIdTrick(),$userRepository);
        return new Response($this->render($this->template, $this->parameters), 400);
    }


    public function getToken(Request $request): ?string
    {
        $cookie = $request->cookies->get("token");
        if (!empty($cookie)) {
            return $cookie;
        }
        return null;
    }

    public function setToken(): void
    {
        $session = new Session();
        if (!$session->isStarted()) {
            $response = new Response();
            $response->headers->setCookie(new Cookie("token", bin2hex(random_bytes(20))));
            $session->set("token", current($response->headers->getCookies())->getValue());
            $response->send();
        }
    }
}

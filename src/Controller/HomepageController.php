<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{
    public string $template = "homepage.twig";

    /**
     * @var array<string,int>
     */
    public array $parameters = [];

    #[Route(path: '/', methods: ["GET"])]
    public function homepage(TrickRepository $trickRepository): Response
    {
        $this->parameters["user_connected"] = !empty($this->getSessionData("user_connected")) ? $this->getSessionData(
            "user_connected"
        ) : '';
        $this->parameters["tricks"] = $trickRepository->getTricks();
        return new Response($this->render($this->template, $this->parameters));
    }

    public function getSessionData(string $name): string|int|null
    {
        $session = new Session();
        if (!$session->isStarted()) {
            return $session->get($name);
        }
        return null;
    }

}

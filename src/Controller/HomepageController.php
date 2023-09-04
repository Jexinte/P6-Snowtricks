<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class HomepageController extends AbstractController
{
    public string $template = "homepage.twig";
    #[Route(path:'/',methods: ["GET"])]
    public function homepage():Response
    {
    return new Response($this->render($this->template,["user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : '']));
    }

    public function getSessionData(string $name):string|int|null
    {
        $session = new Session();
        if(!$session->isStarted()){
            return $session->get($name);
        }
        return null;
    }
}

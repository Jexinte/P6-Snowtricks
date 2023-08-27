<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class HomepageController extends AbstractController
{
    public string $template = "homepage.twig";
    #[Route(path:'/',methods: ["GET"])]
    public function homepage():Response
    {
    return new Response($this->render($this->template));
    }
}

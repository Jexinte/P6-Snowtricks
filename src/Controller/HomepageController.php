<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class HomepageController extends AbstractController
{
    public string $template = "homepage.twig";
    #[Route('/')]
    public function Homepage():Response
    {
    return new Response($this->render($this->template));
    }
}

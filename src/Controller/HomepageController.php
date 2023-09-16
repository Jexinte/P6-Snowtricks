<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{

    private readonly AsciiSlugger $slugger;

    public function __construct()
    {
        $this->slugger = new AsciiSlugger();
    }

    #[Route(path: '/', name: 'homepage', methods: ["GET"])]
    public function homepage(TrickRepository $trickRepository, Request $request): Response
    {
        $template = "homepage.twig";
        $parameters = [];
        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $tricks = $trickRepository->findAll();
        $result = [];
        foreach ($tricks as $k => $value) {
            $result[$k] = [
                "name" => $value->getName(),
                "slug" => strtolower($this->slugger->slug($value->getName())),
                "id" => $value->getId()
            ];
        }
        $parameters["tricks"] = $result;
        return new Response($this->render($template, $parameters));
    }


}

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
    public string $template = "homepage.twig";

    /**
     * @var array<string,int>
     */
    public array $parameters = [];

    #[Route(path: '/', name:'homepage' ,methods: ["GET"])]
    public function homepage(TrickRepository $trickRepository,Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $slug = new AsciiSlugger();
        $tricks = $trickRepository->getTricks();
        $result = [];
        foreach ($tricks as $k => $value) {
            $result[$k] = [
                "name" => $value->getName(),
                "slug" => strtolower($slug->slug($value->getName())),
                "id" => $value->getId()
            ];

        }
        $this->parameters["tricks"] = $result;
        return new Response($this->render($this->template, $this->parameters));
    }



}

<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;

class HomepageController extends AbstractController
{


    #[Route(path: '/', name: 'homepage', methods: ["GET"])]
    public function homepage(
        TrickRepository $trickRepository,
        Request $request,
        MediaRepository $mediaRepository,
        SluggerInterface $slugger
    ): Response {
        $parameters = [];
        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $tricks = $trickRepository->findAll();
        $result = [];
        $medias = $mediaRepository->findAll();

        foreach ($tricks as $k => $value) {
            if ($medias[$k]->getIsBanner() && $value->getId() == $medias[$k]->getIdTrick()) {
                $result[$k] = [
                    "name" => $value->getName(),
                    "slug" => strtolower($slugger->slug($value->getName())),
                    "id" => $value->getId(),
                    "main_banner" => $medias[$k]->getMediaPath()
                ];
            } else {
                $result[$k] = [
                    "name" => $value->getName(),
                    "slug" => strtolower($slugger->slug($value->getName())),
                    "id" => $value->getId(),
                ];
            }
        }
        $parameters["tricks"] = $result;
        return new Response($this->render("homepage.twig", $parameters));
    }


}

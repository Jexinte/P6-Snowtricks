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
        $parameters["user_connected"] = $userConnected;
        $tricks = $trickRepository->findAll();
        $result = [];
        $banners = $mediaRepository->findBy(["isBanner" => true]);

        foreach ($tricks as $k => $trick) {
            if(!empty($banners))
            {
                foreach($banners as $banner){
                    if($banner->getIdTrick() == $trick->getId())
                    {
                        $result[$k] = [
                        "name" => $trick->getName(),
                        "slug" => strtolower($slugger->slug($trick->getName())),
                        "id" => $trick->getId(),
                        "main_banner" => $banner->getMediaPath()
                    ];
                    }
                    else {
                        $result[$k] = [
                            "name" => $trick->getName(),
                            "slug" => strtolower($slugger->slug($trick->getName())),
                            "id" => $trick->getId(),
                        ];
                    }
                }
            }
            else {
                $result[$k] = [
                    "name" => $trick->getName(),
                    "slug" => strtolower($slugger->slug($trick->getName())),
                    "id" => $trick->getId(),
                ];
            }
        }
        $parameters["tricks"] = $result;
        return new Response($this->render("homepage.twig", $parameters));
    }


}

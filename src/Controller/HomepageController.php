<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class HomepageController extends AbstractController
{
    #[Route(path: '/', name: 'homepage', methods: ["GET"])]
    public function homepage(
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
    ): Response {
        $parameters = [];
        $userConnected = !is_null($this->getUser()) ? current($this->getUser()->getRoles()) : '';
        $tricks = $trickRepository->findAll();

        $banners = $mediaRepository->findBy(["isBanner" => true]);

        foreach($tricks as $trick) {
            foreach($banners as $banner) {
                if($trick->getId() == $banner->getIdTrick()) {
                    $trick->banner = $banner->getMediaPath();
                }
            }
        }


        $parameters["tricks"] = $tricks;
        $parameters["user_connected"] = $userConnected;
        return new Response($this->render("homepage.twig", $parameters));
    }


}

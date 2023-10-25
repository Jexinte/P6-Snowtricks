<?php
/**
 * Handle display of tricks
 *
 * PHP version 8
 *
 * @category Controller
 * @package  HomepageController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Controller;

use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Handle display of tricks
 *
 * PHP version 8
 *
 * @category Controller
 * @package  HomepageController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class HomepageController extends AbstractController
{
    /**
     * Summary of homepage
     *
     * @param TrickRepository $trickRepository Object
     * @param MediaRepository $mediaRepository Object
     *
     * @return Response
     */
    #[Route(path: '/', name: 'homepage', methods: ["GET"])]
    public function homepage(
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
    ): Response {
        $parameters = [];
        $userConnected = $this->getUser() ?: '';
        $tricks = $trickRepository->findAll();
        $banners = $mediaRepository->findBy(["isBanner" => true]);

        foreach ($tricks as $trick) {
            foreach ($banners as $banner) {
                if ($trick->getId() == $banner->getIdTrick()
                ) {
                    $trick->banner = $banner->getMediaPath();
                }
            }
        }

        $parameters["tricks"] = $tricks;
        $parameters["user_connected"] = $userConnected;
        return new Response($this->render("homepage.twig", $parameters));
    }


}

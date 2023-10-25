<?php

/**
 * Handle display of error templates
 *
 * PHP version 8
 *
 * @category Controller
 * @package  ErrorController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Enumeration\CodeStatus;

/**
 * Handle display of error templates
 *
 * PHP version 8
 *
 * @category Controller
 * @package  ErrorController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class ErrorController extends AbstractController
{
    /**
     * Summary of errorExceptions
     *
     * @param \Throwable $exception Object
     * 
     * @return RedirectResponse|null
     */
    public function errorExceptions(\Throwable $exception): ?RedirectResponse
    {
        return match (true) {
            $exception instanceof NotFoundHttpException => $this->redirectToRoute('ressource_not_found'),
            default => $this->redirectToRoute("server_down"),
        };
    }


    /**
     * Summary of error401
     *
     * @return Response
     */
    #[Route('/error/401', name: 'unauthorized', methods: ["GET"])]
    public function error401(): Response
    {
        return new Response($this->render('/bundles/TwigBundle/Exception/error401.html.twig'), CodeStatus::UNAUTHORIZED);
    }

    /**
     * Summary of error404
     *
     * @return Response
     */
    #[Route('/error/404', name: 'ressource_not_found', methods: ["GET"])]
    public function error404(): Response
    {
        return new Response(
            $this->render('/bundles/TwigBundle/Exception/error404.html.twig'),
            CodeStatus::RESSOURCE_NOT_FOUND
        );
    }

    /**
     * Summary of error500
     *
     * @return Response
     */
    #[Route('/error/500', name: 'server_down', methods: ["GET"])]
    public function error500(): Response
    {
        return new Response($this->render('/bundles/TwigBundle/Exception/error.html.twig'), CodeStatus::SERVER);
    }


}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Enumeration\CodeStatus;

class ErrorController extends AbstractController
{


    public function errorExceptions(\Throwable $exception): ?RedirectResponse
    {
        return match (true) {
            $exception instanceof NotFoundHttpException => $this->redirectToRoute('ressource_not_found'),
            default => $this->redirectToRoute("server_down"),
        };
    }

    #[Route('/error/404', name: 'ressource_not_found', methods: ["GET"])]
    public function error404(): Response
    {
        return new Response(
            $this->render('/bundles/TwigBundle/Exception/error404.html.twig'),
            CodeStatus::RESSOURCE_NOT_FOUND
        );
    }


    #[Route('/error/500', name: 'server_down', methods: ["GET"])]
    public function error500(): Response
    {
        return new Response($this->render('/bundles/TwigBundle/Exception/error.html.twig'), CodeStatus::SERVER);
    }


}

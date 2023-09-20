<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
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
            $exception instanceof AccessDeniedException => $this->redirectToRoute("forbidden"),
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

    #[Route('/error/403', name: 'forbidden', methods: ["GET"])]
    public function error403(): Response
    {
        return new Response($this->render('/bundles/TwigBundle/Exception/error403.html.twig'), CodeStatus::FORBIDDEN);
    }

    #[Route('/error/500', name: 'server_down', methods: ["GET"])]
    public function error500(): Response
    {
        return new Response($this->render('/bundles/TwigBundle/Exception/error.html.twig'), CodeStatus::SERVER);
    }


}

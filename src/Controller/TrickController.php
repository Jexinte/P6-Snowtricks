<?php

namespace App\Controller;

use App\Controller\DTO\TrickDTO;
use App\Enumeration\UserStatus;
use App\Enumeration\CodeStatus;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class TrickController extends AbstractController
{
    public string $template = "";
    /**
     * @var array<string,int>
     */
    public array $parameters = [];


    #[Route('/trick/delete/{id}', methods: ["POST"])]
    public function deleteTrick(
        ?int $id,
        TrickRepository $trickRepository,
        TrickDTO $trickDTO
    ): Response|RedirectResponse {
        $result = "";
        if (!is_null($id)) {
            $trickDTO->setId($id);
            $result = $trickRepository->deleteTrick($trickDTO);
        }
        return array_key_exists("trick_delete", $result) && $result["trick_delete"] ? new RedirectResponse(
            '/',
            CodeStatus::REDIRECT
        ) : new RedirectResponse('/error/' . CodeStatus::RESSOURCE_NOT_FOUND);
    }
}

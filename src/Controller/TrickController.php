<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;

class TrickController extends AbstractController
{
    public string $template = "";
    /**
     * @var array<string,int>
     */
    public array $parameters = [];

    #[Route('/{trickname}/details/{id}',name:'trick', methods: ["GET"])]
    public function getTrick(
        string $trickname,
        int $id,
        TrickRepository $trickRepository,
        Trick $trickEntity,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        Request $request
    ): Response {
        $userConnected = $request->getSession()->get('user_connected');
        $trick = $trickRepository->getTrick($id);
        $trickComments = $commentRepository->getComments($id,$userRepository);
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $frenchDateFormat = new IntlDateFormatter('fr_Fr', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $dateTrick = $trick->getDate();
        $date = $frenchDateFormat->format($dateTrick);
        $this->template = "trick.twig";
        $this->parameters["trick"] = $trick;
        $this->parameters["trick_date"] = $date;
        $this->parameters["comments"] = $trickComments;
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $request->getSession()->set("trick",$trick);
        $request->getSession()->set("trick_date",$date);
        return new Response($this->render($this->template, $this->parameters));
    }



    #[Route('/trick/delete/{id}', methods: ["POST"])]
    public function deleteTrick(
        ?int $id,
        TrickRepository $trickRepository,
    ): Response|RedirectResponse {
        if (!is_null($id)) {
            $trick = $trickRepository->find(["id" => $id]);
            $trickRepository->getEntityManager()->remove($trick);
            $trickRepository->getEntityManager()->flush();
            return $this->redirectToRoute('homepage');
        }
        throw $this->createNotFoundException();
    }
}

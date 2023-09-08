<?php

namespace App\Controller;

use App\Entity\Media;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrickController extends AbstractController
{
    public string $template = "";
    /**
     * @var array<string,int>
     */
    public array $parameters = [];

    #[Route('/{trickname}/details/{id}', name: 'trick', methods: ["GET"])]
    public function getTrickPage(
        string $trickname,
        int $id,
        TrickRepository $trickRepository,
        Trick $trickEntity,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        MediaRepository $mediaRepository,
        Request $request
    ): Response {
        $userConnected = $request->getSession()->get('user_connected');
        $trick = $trickRepository->getTrick($id);
        $medias = $mediaRepository->getTrickMedia($id);
        $trickComments = $commentRepository->getComments($id, $userRepository);
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $frenchDateFormat = new IntlDateFormatter('fr_Fr', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $dateTrick = $trick->getDate();
        $date = $frenchDateFormat->format($dateTrick);
        $this->template = "trick.twig";
        $this->parameters["trick"] = $trick;
        $this->parameters["medias"] = $medias;
        $this->parameters["trick_date"] = $date;
        $this->parameters["comments"] = $trickComments;
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $request->getSession()->set("trick", $trick);
        $request->getSession()->set("trick_date", $date);
        return new Response($this->render($this->template, $this->parameters));
    }


    #[Route('/create-trick', name: 'create_trick_get', methods: ["GET"])]
    public function createTrickPage(): Response
    {
        $this->template = "create_trick.twig";
        return new Response($this->render($this->template));
    }


    #[Route('/create-trick', name: 'create_trick_post', methods: ["POST"])]
    public function createTrickSubmit(
        Request $request,
        ValidatorInterface $validator,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response {
        $this->template = "create_trick.twig";

        $trickEntity = new Trick();
        $trickEntity->setName($request->request->get('trick-name'));
        $trickEntity->setDescription($request->request->get('description'));
        $trickEntity->setTrickGroup($request->request->get('selected_options'));
        $trickEntity->setImages($request->files->get("images"));
        $trickEntity->setVideos($request->files->get("videos"));
        $trickEntity->setEmbedUrl($request->request->get("youtube-url"));

        $mainBanner = $request->files->get("main-banner");
        if(!is_null($mainBanner))
        {
            $trickEntity->setMainBannerFile($mainBanner);
        }
        $numberOfErrors = 0;
        $groups = [
            "name_exception",
            "description_exception",
            "illustration_exception",
            "video_exception",
            "group_exception",
            'url_exception',
            'main_banner_exception'
        ];
        $groupsViolations = [];
        foreach ($groups as $group) {
            $errors = $validator->validate($trickEntity, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }
        if ($numberOfErrors == 0) {
            preg_match('/<iframe[^>]+src="([^"]+)"/i', $trickEntity->getEmbedUrl(), $matches);
            $actualDate = new \DateTime();
            $url = empty($trickEntity->getEmbedUrl()) ? null : $matches[1];
            $trickEntity->setEmbedUrl($url);
            $trickEntity->setDate($actualDate);
            $trickCreated = $trickRepository->createTrick($trickEntity);

            $media = new Media();
            $media->setIdTrick($trickCreated);
            $media->setVideos($trickEntity->getVideos());
            $media->setImages($trickEntity->getImages());
            $mediaRepository->saveTrickMedias($media);

            return $this->redirectToRoute('homepage');
        }


        $this->parameters["exceptions"] = $groupsViolations;
        return new Response($this->render($this->template, $this->parameters), 400);
    }

    #[Route('/trick/update/{id}',methods: ["GET"])]
    public function getUpdatePage(int $id,TrickRepository $trickRepository,MediaRepository $mediaRepository):Response
    {
        $this->template="update_trick.twig";
        $trick = $trickRepository->getTrick($id);
        $medias = $mediaRepository->getTrickMedia($id);
        $this->parameters["trick"] = $trick;
        $this->parameters["medias"] = $medias;
        return new Response($this->render($this->template,$this->parameters));
    }

    #[Route('/trick/update/{id}/',name:'update_trick_post',methods: ["POST"])]

    public function updateTrickSubmit(int $id,Request $request):void
    {

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

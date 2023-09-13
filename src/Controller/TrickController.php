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

        if($request->query->get('page') !== null && !empty($request->query->get('page'))){
            $currentPage = $request->query->get('page');
        } else{
            $currentPage = 1;
        }
        $nbComments = count($trickComments);
        $commentsPerPage = 10;
        $pages = ceil($nbComments/$commentsPerPage);
        $firstPage = ($currentPage * $commentsPerPage) - $commentsPerPage;
        $commentsPerPageRequest = $commentRepository->getCommentsPerPage($firstPage,$commentsPerPage);
        $this->parameters["comments"] = $commentsPerPageRequest;
        $this->parameters["pages"] = $pages;
        $this->parameters["currentPage"] = $currentPage;
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $frenchDateFormat = new IntlDateFormatter('fr_Fr', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $dateTrick = $trick->getDate();
        $date = $frenchDateFormat->format($dateTrick);
        $request->getSession()->set("more_than_ten_comments",false);
        $this->template = "trick.twig";
        $this->parameters["trick"] = $trick;
        $this->parameters["medias"] = $medias;
        $this->parameters["trick_date"] = $date;



        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';

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

        $mediaEntity = new Media();
        $mediaEntity->setIllustrations($request->files->get('images'));
        $mediaEntity->setBannerFile($request->files->get('image'));
        $mediaEntity->setVideos($request->files->get('videos'));
        $mediaEntity->setEmbedUrl($request->request->get('embed-url'));


        $numberOfErrors = 0;
        $groups = [
            "name_exception",
            "description_exception",
            "illustration_exception",
            "video_exception",
            "group_exception",
            'url_exception',
            'banner_exception'
        ];
        $groupsViolations = [];

        foreach ($groups as $group) {
            $errorTrickEntity = $validator->validate($trickEntity, null, $group);
            $errorMediaEntity = $validator->validate($mediaEntity, null, $group);
            if (count($errorTrickEntity) >= 1) {
                $numberOfErrors++;
            }
            if (count($errorMediaEntity) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errorTrickEntity as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
            foreach ($errorMediaEntity as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }
        if ($numberOfErrors == 0) {
            $actualDate = new \DateTime();
            if (!empty($mediaEntity->getEmbedUrl())) {
                preg_match('/<iframe[^>]+src="([^"]+)"/i', $mediaEntity->getEmbedUrl(), $matches);
                $urlCleaned = $matches[1];
                $mediaEntity->setEmbedUrl($urlCleaned);
            }

            $trickEntity->setDate($actualDate);
            $trickCreated = $trickRepository->createTrick($trickEntity);
            $mediaEntity->setIdTrick($trickCreated);
            $mediaRepository->saveTrickMedias($mediaEntity);


            return $this->redirectToRoute('homepage');
        }


        $this->parameters["exceptions"] = $groupsViolations;
        return new Response($this->render($this->template, $this->parameters), 400);
    }

    #[Route('/update-trick/{trickname}/{id}', name: 'update_trick_get', methods: ["GET"])]
    public function updateTrickPage(
        int $id,
        string $trickname,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        Request $request
    ): Response {
        $this->template = "update_trick.twig";
        $userConnected = $request->getSession()->get('user_connected');
        $trick = $trickRepository->getTrick($id);
        $medias = $mediaRepository->getTrickMedia($id);
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $frenchDateFormat = new IntlDateFormatter('fr_Fr', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $dateTrick = $trick->getDate();
        $date = $frenchDateFormat->format($dateTrick);
        $this->parameters["trick"] = $trick;
        $this->parameters["medias"] = $medias;
        $this->parameters["trick_date"] = $date;
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render($this->template, $this->parameters));
    }

    #[Route('/update-trick-media/{id}', name: 'update_trick_media_get', methods: ["GET"])]
    public function updateTrickMediaPage(int $id, MediaRepository $mediaRepository): Response
    {
        $media = $mediaRepository->findBy(["id" => $id]);
        $this->template = "update_media.twig";
        $this->parameters["media"] = current($media);
        return new Response($this->render($this->template, $this->parameters));
    }

    #[Route('/update-trick-media/{id},', name: 'update_trick_media_put', methods: ["PUT"])]
    public function updateTrickMediaValidator(
        int $id,
        Request $request,
        ValidatorInterface $validator,
        MediaRepository $mediaRepository
    ): Response {
        $this->template = "update_media.twig";
        $file = $request->files->get('file');
        $embedUrl = $request->request->get('embed-url');
        $numberOfErrors = 0;
        $groups = [
            "update_file_exception",
            "url_exception"
        ];
        $groupsViolations = [];
        $mediaEntity = new Media();
        $media = $mediaRepository->findBy(["id" => $id]);

        switch (true) {
            case !empty($file):
                $mediaEntity->setUpdatedFile($file);
                foreach ($groups as $group) {
                    $errors = $validator->validate($mediaEntity, null, $group);
                    if (count($errors) >= 1) {
                        $numberOfErrors++;
                    }
                    foreach ($errors as $error) {
                        $groupsViolations[$group] = $error->getMessage();
                    }
                }

                if ($numberOfErrors == 0) {
                    $fileUpdated = $mediaRepository->updateTrickMedia($id, $mediaEntity);
                    if ($fileUpdated) {
                        $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                        return $this->redirectToRoute('homepage');
                    }
                }
                break;
            case !empty($embedUrl):
                $mediaEntity->setEmbedUrl($embedUrl);
                foreach ($groups as $group) {
                    $errors = $validator->validate($mediaEntity, null, $group);
                    if (count($errors) >= 1) {
                        $numberOfErrors++;
                    }
                    foreach ($errors as $error) {
                        $groupsViolations[$group] = $error->getMessage();
                    }
                }

                if ($numberOfErrors == 0) {
                    preg_match('/<iframe[^>]+src="([^"]+)"/i', $mediaEntity->getEmbedUrl(), $matches);
                    $urlCleaned = $matches[1];
                    $mediaEntity->setEmbedUrl($urlCleaned);
                    $urlUpdated = $mediaRepository->updateTrickMedia($id, $mediaEntity);

                    if ($urlUpdated) {
                        $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                        return $this->redirectToRoute('homepage');
                    }
                }
                break;
            default:
                $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                return $this->redirectToRoute('homepage');
        }

        $this->parameters["media"] = current($media);
        $this->parameters["exceptions"] = $groupsViolations;
        return new Response($this->render($this->template, $this->parameters), 400);
    }

    #[Route('/update-trick-content/{trickname}/{id}', name: 'update_trick_content_put', methods: ["PUT"])]
    public function updateTrickContentValidator(
        int $id,
        string $trickname,
        Request $request,
        ValidatorInterface $validator,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response {
        $this->template = "update_trick.twig";
        $numberOfErrors = 0;
        $trick = $trickRepository->getTrick($id);
        $media = $mediaRepository->getTrickMedia($id);
        $trickEntity = new Trick();
        $trickEntity->setNameUpdated($request->request->get('trick-name'));
        $trickEntity->setDescription($request->request->get('description'));
        $trickEntity->setTrickGroup($request->request->get('selected_options'));
        $groups = [
            "namex_exception",
            "description_exception",
            "group_exception",
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
            $trickUpdated = $trickRepository->updateTrick($id, $trickEntity);
            if ($trickUpdated) {
                $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                return $this->redirectToRoute('homepage');
            }
        }
        $userConnected = $request->getSession()->get('user_connected');
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $this->parameters["medias"] = $media;
        $this->parameters["trick"] = $trick;
        $this->parameters["exceptions"] = $groupsViolations;
        return new Response($this->render($this->template, $this->parameters), 400);
    }

    #[Route('/trick/delete/{trickname}/{id}', name: 'delete_trick', methods: ["DELETE"])]
    public function deleteTrick(
        ?int $id,
        string $trickname,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response|RedirectResponse {
        if (is_null($id)) {
            throw $this->createNotFoundException();
        }

        $trick = $trickRepository->find(["id" => $id]);
        $medias = $mediaRepository->getTrickMedia($id);
        foreach ($medias as $media) {
            if ($media->getMediaType() != "web") {
                unlink("../public" . $media->getMediaPath());
            }
            $mediaRepository->getEntityManager()->remove($media);
        }
        $mediaRepository->getEntityManager()->flush();

        $trickRepository->getEntityManager()->remove($trick);
        $trickRepository->getEntityManager()->flush();
        $this->addFlash("success", "La suppression du trick a bien été prise en compte !");
        return $this->redirectToRoute('homepage');
    }

    #[Route('/delete-trick-media/{id}', name: 'delete_trick_media', methods: ["DELETE"])]
    public function deleteTrickMedia(int $id, MediaRepository $mediaRepository): Response
    {
        $media = current($mediaRepository->findBy(["id" => $id]));
        if($media->getMediaType() != "web")
        {
            unlink("../public" . $media->getMediaPath());

        }
        $mediaRepository->getEntityManager()->remove($media);
        $mediaRepository->getEntityManager()->flush();
        $this->addFlash("success", "La suppression du média a bien été prise en compte !");
        return $this->redirectToRoute('homepage');
    }
}

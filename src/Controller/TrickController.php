<?php
/**
 * Handle tricks
 *
 * PHP version 8
 *
 * @category Controller
 * @package  TrickController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Controller;

use App\Form\Type\AddComment;
use App\Form\Type\CreateTrick;
use App\Form\Type\CreateTrickMedia;
use App\Service\FileGenerator;
use App\Form\Type\UpdateTrickContent;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;

/**
 * Handle tricks
 *
 * PHP version 8
 *
 * @category Controller
 * @package  TrickController
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class TrickController extends AbstractController
{
    /**
     * Summary of getTrickPage
     *
     * @param Trick             $trick             Object
     * @param UserRepository    $userRepository    Object
     * @param CommentRepository $commentRepository Object
     * @param MediaRepository   $mediaRepository   Object
     * @param IntlDateFormatter $dateFormatter     Object
     * @param Request           $request           Object
     *
     * @return Response
     */
    #[Route('/{slug}/details/{id}', name: 'trick', methods: ["GET"])]
    public function getTrickPage(
        Trick $trick,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        MediaRepository $mediaRepository,
        IntlDateFormatter $dateFormatter,
        Request $request
    ): Response {
        $userConnected = $this->getUser() ?: '';
        $form = $this->createForm(AddComment::class);
        $id = $trick->getId();
        $medias = $mediaRepository->findBy(
            ["idTrick" => $id, "isBanner" => null]
        );
        $mainBannerOfTrick = current(
            $mediaRepository->findBy(["idTrick" => $id, "isBanner" => true])
        );
        $trickComments = $commentRepository->getComments($id, $userRepository);
        if ($request->query->get(
            'page'
        ) !== null && !empty($request->query->get('page'))
        ) {
            $currentPage = $request->query->get('page');
        } else {
            $currentPage = 1;
        }
        $nbComments = count($trickComments);
        $commentsPerPage = 10;
        $pages = ceil($nbComments / $commentsPerPage);
        $firstPage = ($currentPage * $commentsPerPage) - $commentsPerPage;
        $commentsPerPageRequest = $commentRepository->getCommentsPerPage(
            $id,
            $firstPage,
            $commentsPerPage
        );
        foreach ($commentsPerPageRequest as $comment) {
            $comment->date = ucfirst(
                $dateFormatter->format($comment->getCreatedAt())
            );
        }
        $parameters["comments"] = $commentsPerPageRequest;
        $parameters["pages"] = $pages;
        $parameters["currentPage"] = $currentPage;
        $dateTrick = ucfirst($dateFormatter->format($trick->getCreatedAt()));
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        $parameters["totalComments"] = $nbComments;
        $parameters["banner"] = $mainBannerOfTrick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $dateTrick;
        $parameters["user_connected"] = $userConnected;

        return new Response($this->render("trick.twig", $parameters));
    }


    /**
     * Summary of createTrickPage
     * 
     * @return Response
     */
    #[Route('/create-trick', name: 'create_trick_get', methods: ["GET"])]
    public function createTrickPage(): Response
    {
        $userConnected = $this->getUser() ?: '';
        $form = $this->createForm(CreateTrick::class);
        $form->add('mediaForm', CreateTrickMedia::class);
        $parameters["user_connected"] = $userConnected;
        $parameters["form"] = $form;
        return new Response($this->render("create_trick.twig", $parameters));
    }


    /**
     * Summary of createTrick
     *
     * @param Request         $request         Object
     * @param TrickRepository $trickRepository Object
     * @param MediaRepository $mediaRepository Object
     * @param FileGenerator   $fileGenerator   Object
     * @param DateTime        $dateTime        Object
     * 
     * @return Response
     */
    #[Route('/create-trick', name: 'create_trick_post', methods: ["POST"])]
    public function createTrick(
        Request $request,
        FileGenerator $fileGenerator,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        DateTime $dateTime
    ): Response {
        $userConnected = $this->getUser() ?: '';
        $form = $this->createForm(CreateTrick::class);
        $form->add('mediaForm', CreateTrickMedia::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setCreatedAt($dateTime);
            $media = $form->get('mediaForm')->getData();
            if (!empty($media->getEmbedUrl())) {
                preg_match(
                    '/<iframe[^>]+src="([^"]+)"/i',
                    $media->getEmbedUrl(),
                    $matches
                );
                $urlCleaned = $matches[1];
                $media->setEmbedUrl($urlCleaned);
            }

            $fileGenerator->saveBanner($media, $trick, $mediaRepository);
            $fileGenerator->saveEmbedUrl($media, $trick, $mediaRepository);
            $fileGenerator->saveImages($media, $trick, $mediaRepository);
            $fileGenerator->saveVideos($media, $trick, $mediaRepository);

            $trickRepository->getEntityManager()->persist($trick);
            $trickRepository->getEntityManager()->flush();
            $this->addFlash(
                "success",
                "Votre nouveau trick a été créé avec succès !"
            );
            return $this->redirectToRoute('homepage');
        }

        $parameters["user_connected"] = $userConnected;
        $parameters["form"] = $form;
        return new Response(
            $this->render("create_trick.twig", $parameters),
            Response::HTTP_BAD_REQUEST
        );
    }



    /**
     * Summary of updateTrickPage
     *
     * @param Trick             $trick           Object
     * @param MediaRepository   $mediaRepository Object
     * @param IntlDateFormatter $dateFormatter   Object
     *
     * @return Response
     */
    #[Route('/update-trick/{slug}/details/{id}', name: 'update_trick_get', methods: ["GET"])]
    public function updateTrickPage(
        Trick $trick,
        MediaRepository $mediaRepository,
        IntlDateFormatter $dateFormatter
    ): Response {
        $userConnected = $this->getUser() ?: '';

        $form = $this->createForm(UpdateTrickContent::class, $trick);
        $medias = $mediaRepository->findBy(
            ["idTrick" => $trick->getId(), "isBanner" => null]
        );
        $mainBannerOfTrick = current(
            $mediaRepository->findBy(
                ["idTrick" => $trick->getId(), "isBanner" => true]
            )
        );
        $dateTrick = $trick->getCreatedAt();
        $date = $dateFormatter->format($dateTrick);
        $parameters["trick"] = $trick;
        $parameters["banner"] = $mainBannerOfTrick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $date;
        $parameters["form"] = $form;
        $parameters["user_connected"] = $userConnected;
        return new Response($this->render("update_trick.twig", $parameters));
    }


    /**
     * Summary of updateTrickContentValidator
     *
     * @param Trick           $trick           Object
     * @param Request         $request         Object
     * @param TrickRepository $trickRepository Object
     * @param MediaRepository $mediaRepository Object
     * @param DateTime        $dateTime        Object
     *
     * @return Response
     */
    #[Route('/update-trick-content/{slug}/details/{id}', name: 'update_trick_content_put', methods: ["PUT"])]
    public function updateTrickContentValidator(
        Trick $trick,
        Request $request,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        DateTime $dateTime
    ): Response {
        $media = $mediaRepository->findBy(
            ["idTrick" => $trick->getId(), "isBanner" => null]
        );
        $mainBannerOfTrick = current(
            $mediaRepository->findBy(
                ["idTrick" => $trick->getId(), "isBanner" => true]
            )
        );
        $form = $this->createForm(UpdateTrickContent::class, $trick);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $trickEntity = $form->getData();
            $trickEntity->setUpdatedAt($dateTime);
            $trickRepository->getEntityManager()->flush();
            $this->addFlash(
                "success",
                "Votre trick a été mis à jour avec succès !"
            );
            return $this->redirectToRoute('homepage');
        }

        $userConnected = $this->getUser() ?: '';
        $parameters["user_connected"] = $userConnected;
        $parameters["medias"] = $media;
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        $parameters["banner"] = $mainBannerOfTrick;
        return new Response(
            $this->render("update_trick.twig", $parameters), 400
        );
    }

    /**
     * Summary of deleteTrick
     *
     * @param Trick           $trick           Object
     * @param TrickRepository $trickRepository Object
     * @param MediaRepository $mediaRepository Object
     * 
     * @return Response|RedirectResponse
     */
    #[Route('/trick/delete/{slug}/{id}', name: 'delete_trick', methods: ["DELETE"])]
    public function deleteTrick(
        Trick $trick,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response|RedirectResponse {
        $medias = $mediaRepository->findBy(["idTrick" => $trick->getId()]);

        foreach ($medias as $media) {
            if ($media->getMediaType() != "web") {
                unlink("../public" . $media->getMediaPath());
            }
        }
        $trickRepository->getEntityManager()->remove($trick);
        $trickRepository->getEntityManager()->flush();

        $this->addFlash("success", "Votre trick a été supprimé avec succès !");
        return $this->redirectToRoute('homepage');
    }


}

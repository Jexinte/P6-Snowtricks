<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\Type\AddComment;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use IntlDateFormatter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Trick;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TrickController extends AbstractController
{

    private Trick $trick;
    private IntlDateFormatter $dateFormatter;

    public function __construct()
    {
        $this->trick = new Trick();
        $this->dateFormatter = new IntlDateFormatter('fr_Fr', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
    }

    #[Route('/{trickname}/details/{id}', name: 'trick', methods: ["GET"])]
    public function getTrickPage(
        string $trickname,
        int $id,
        TrickRepository $trickRepository,
        UserRepository $userRepository,
        CommentRepository $commentRepository,
        MediaRepository $mediaRepository,
        Request $request
    ): Response {
        $template = "trick.twig";
        $form = $this->createForm(AddComment::class);
        $userConnected = $request->getSession()->get('user_connected');
        $trick = $trickRepository->getTrick($id);
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
        $trickComments = $commentRepository->getComments($id, $userRepository);

        if ($request->query->get('page') !== null && !empty($request->query->get('page'))) {
            $currentPage = $request->query->get('page');
        } else {
            $currentPage = 1;
        }
        $nbComments = count($trickComments);
        $commentsPerPage = 10;
        $pages = ceil($nbComments / $commentsPerPage);
        $firstPage = ($currentPage * $commentsPerPage) - $commentsPerPage;
        $commentsPerPageRequest = $commentRepository->getCommentsPerPage($firstPage, $commentsPerPage);
        $parameters["comments"] = $commentsPerPageRequest;
        $parameters["pages"] = $pages;
        $parameters["currentPage"] = $currentPage;
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $frenchDateFormat = new IntlDateFormatter('fr_Fr', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $dateTrick = $trick->getDate();
        $date = $frenchDateFormat->format($dateTrick);
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $date;


        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';

        return new Response($this->render($template, $parameters));
    }


    #[Route('/create-trick', name: 'create_trick_get', methods: ["GET"])]
    public function createTrickPage(): Response
    {
        $template = "create_trick.twig";
        return new Response($this->render($template));
    }


    #[Route('/create-trick', name: 'create_trick_post', methods: ["POST"])]
    public function createTrickSubmit(
        Request $request,
        ValidatorInterface $validator,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response {
        $template = "create_trick.twig";

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


        $parameters["exceptions"] = $groupsViolations;
        return new Response($this->render($template, $parameters), 400);
    }

    public function initializeUpdateTrickContentForm(Trick $trick): FormBuilderInterface
    {
        return $this->createFormBuilder()
            ->add("nameupdated", TextType::class, options: [
                'label' => 'Nom du trick',
                'required' => false,
                'constraints' => [
                    new Regex(
                        pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
                        message: 'Oops! Le format de votre saisie est incorrect, le nom du trick doit commencer par une lettre majuscule',
                        match: true,
                    )
                ],
                "attr" => ["value" => $trick->getName()]
            ])
            ->add('description', TextareaType::class, options: [
                'label' => 'Description',
                'required' => false,
                'constraints' =>
                    [
                        new Regex(
                            pattern: "/^[A-ZÀ-ÿ][A-Za-zÀ-ÿ, .'\-\n]*$/u",
                            message: 'Oops! Le format de votre saisie est incorrect, votre description doit commencer par une lettre majuscule',
                            match: true,
                        )
                    ],
                "data" => $trick->getDescription(),
            ])
            ->add('trickGroup', ChoiceType::class, options: [
                'label' => 'Sélectionner un groupe',
                "choices" => [
                    $trick->getTrickGroup() => true,
                    "Grabs" => "Grabs",
                    "Rotations" => "Rotations",
                    "Flips" => "Flips",
                    "Rotation désaxées" => "Rotation désaxées",
                    "Slides" => "Slides",
                    "One Foot Tricks" => "One Foot Tricks",
                    "Old School" => "Old School"
                ]
            ])->add('save', SubmitType::class, ['label' => 'Envoyer', 'attr' => ['class' => 'btn btn-dark']]
            )->setMethod("PUT");
    }

    #[Route('/update-trick/{trickname}/{id}', name: 'update_trick_get', methods: ["GET"])]
    public function updateTrickPage(
        int $id,
        string $trickname,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        Request $request
    ): Response {
        $template = "update_trick.twig";
        $userConnected = $request->getSession()->get('user_connected');
        $trick = current($trickRepository->findBy(["id" => $id]));
        $form = $this->initializeUpdateTrickContentForm($trick)->getForm();
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
        $trick->setName(str_replace('-', ' ', ucfirst($trickname)));
        $dateTrick = $trick->getDate();
        $date = $this->dateFormatter->format($dateTrick);
        $parameters["trick"] = $trick;
        $parameters["medias"] = $medias;
        $parameters["trick_date"] = $date;
        $parameters["form"] = $form;
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render($template, $parameters));
    }


    #[Route('/update-trick-content/{trickname}/{id}', name: 'update_trick_content_put', methods: ["PUT"])]
    public function updateTrickContentValidator(
        int $id,
        string $trickname,
        Request $request,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository
    ): Response {
        $template = "update_trick.twig";
        $trick = current($trickRepository->findBy(["id" => $id]));
        $media = $mediaRepository->findBy(["idTrick" => $id]);
        $formBuilder = $this->initializeUpdateTrickContentForm($trick);
        $form = $formBuilder->getForm();
        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            $token = $request->request->all()["form"]["_token"];
            if ($this->isCsrfTokenValid("form", $token)) {
                $this->trick->setNameUpdated($form->getData()["nameupdated"]);
                $this->trick->setDescription($form->getData()["description"]);
                $this->trick->setTrickGroup($form->getData()["trickGroup"]);
                $trickRepository->updateTrick($id, $this->trick);
                $this->addFlash("success", "Votre fichier a bien été mis à jour !");
                return $this->redirectToRoute('homepage');
            }
        }

        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["medias"] = $media;
        $parameters["form"] = $form;
        $parameters["trick"] = $trick;
        return new Response($this->render($template, $parameters), 400);
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
        $medias = $mediaRepository->findBy(["idTrick" => $id]);
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


}

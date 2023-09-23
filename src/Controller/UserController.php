<?php

namespace App\Controller;

use App\Entity\User;
use App\Enumeration\UserStatus;
use App\Enumeration\CodeStatus;
use App\Form\Type\ForgotPassword;
use App\Form\Type\ResetPassword;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Form\Type\SignUp;
use App\Form\Type\Login;

class UserController extends AbstractController
{


    #[Route(path: '/signup', name: 'registration_get', methods: ["GET"])]
    public function signUpPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $user = new User();
        $form = $this->createForm(SignUp::class, $user);
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["form"] = $form;
        return new Response($this->render("sign_up.twig", $parameters));
    }

    #[Route(path: '/signin', name: 'login_get', methods: ["GET"])]
    public function signInPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $user = new User();
        $form = $this->createForm(Login::class, $user);
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["form"] = $form;
        return new Response($this->render("sign_in.twig", $parameters));
    }

    #[Route(path: '/forgot-password', name: 'forgot_password_get', methods: ["GET"])]
    public function forgotPasswordPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $form = $this->createForm(ForgotPassword::class);
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["form"] = $form;
        return new Response($this->render("forgot_password.twig", $parameters));
    }

    #[Route(path: '/reset-password/token/{id}', methods: ["GET"])]
    public function resetPasswordPage(Request $request, string $id = null): Response|RedirectResponse
    {
        $tokenInSession = $request->getSession()->get('token');
        $userConnected = $request->getSession()->get('user_connected');
        if (!is_null($id) && $id == $tokenInSession) {
            $form = $this->createForm(ResetPassword::class);
            $parameters["token"] = $id;
            $parameters["form"] = $form;
            $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
            return new Response($this->render("reset_password.twig", $parameters));
        }
        throw $this->createNotFoundException();
    }


    #[Route(path: '/signup/registration', name: 'registration', methods: ['POST'])]
    public function signUpValidator(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer,

    ): ?Response {
        $user = new User();
        $form = $this->createForm(SignUp::class, $user);
        $form->handleRequest($request);
        $token = $request->request->all()["sign_up"]["_token"];

        if ($form->isSubmitted() && $form->isValid() && $this->isCsrfTokenValid("sign_up", $token)) {
            $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $user = $form->getData();
            $fileExt = explode('.', $user->getFile()->getClientOriginalName());
            $filename = str_replace("/", "", base64_encode(random_bytes(9))) . '.' . $fileExt[1];
            $imgPath = "/assets/img/$filename";
            $user->setProfileImage($imgPath);
            $user->setStatus(UserStatus::ACCOUNT_NOT_ACTIVATE);
            $tmp = $user->getFile()->getPathname();
            $dir = "../public/assets/img";
            move_uploaded_file($tmp, "$dir/$filename");
            $userRepository->getEntityManager()->persist($user);
            $userRepository->getEntityManager()->flush();
            $user->setCreated(true);
            $this->sendMailToUser($mailer, $user, $request);
            return $this->redirectToRoute("homepage");
        }
        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';

        $parameters["form"] = $form;
        return new Response($this->render("sign_up.twig", $parameters), CodeStatus::CLIENT);
    }


    private function getToken(Request $request): ?string
    {
        $cookie = $request->cookies->get("token");
        if (!empty($cookie)) {
            return $cookie;
        }
        return null;
    }

    private function setToken(): void
    {
        $session = new Session();
        $response = new Response();
        $response->headers->setCookie(new Cookie("token", bin2hex(random_bytes(20))));
        $session->set("token", current($response->headers->getCookies())->getValue());
        $response->send();
    }


    public function sendMailToUser(MailerInterface $mailer, User $user, Request $request): void
    {
        if ($user->isCreated()) {
            $this->setToken();
            $request->getSession()->set("username", $user->getName());
            $email = (new Email())
                ->from("snowtricks@gmail.com")
                ->to($user->getEmail())
                ->subject('Confirmation d\'inscription')
                ->text(
                    body: "Bonjour " . $user->getName() . ",\n
Nous vous remercions de vous être inscrit(e) sur notre plateforme. Avant de pouvoir accéder à toutes les fonctionnalités, nous vous prions de bien vouloir valider votre compte en cliquant sur le lien ci-dessous :\n 
http://127.0.0.1:8000/signup/account-validation\n
Si vous ne pouvez pas cliquer sur le lien, veuillez copier et coller l'URL dans la barre d'adresse de votre navigateur.\n
L'équipe Snowtricks
"
                );
            $mailer->send($email);
        }
    }

    #[Route(path: '/signup/account-validation', methods: ["GET"])]
    public function checkToken(Request $request, UserRepository $userRepository): ?Response
    {
        $cookie = $this->getToken($request);
        $tokenInSession = $request->getSession()->get('token');
        $response = new Response();
        if (!is_null($cookie) && $cookie == $tokenInSession) {
            $userStatusUpdated = $userRepository->updateUserStatus($request);
            if ($userStatusUpdated) {
                $request->getSession()->remove("token");
                $response->headers->clearCookie("token");
                $response->send();
                $parameters["token_authenticated"] = 1;
                return new Response(
                    $this->render(
                        "account_validation.twig",
                        $parameters
                    )
                );
            }
        }
        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response(
            $this->render(
                "account_validation.twig",
                $parameters
            ), CodeStatus::CLIENT
        );
    }

    #[Route(path: '/signin/validation', name: 'login', methods: ["POST"])]
    public function signInValidator(
        Request $request,
        UserRepository $userRepository,
    ): ?Response {
        $parameters = [];
        $user = new User();
        $form = $this->createForm(Login::class, $user);
        $token = $request->request->all()["login"]["_token"];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isCsrfTokenValid("login", $token)) {
                $userData = $form->getData();

                $result = $userRepository->login($userData);

                switch (true) {
                    case $result->getCredentialsValid():
                        $request->getSession()->set("user_id", $result->getUserId());
                        $request->getSession()->set("user_connected", 1);
                        return $this->redirectToRoute('homepage');

                    case !$result->getNameExist() && !is_null($result->getNameExist()):
                        $field = $form->get('name');
                        $error = new FormError(
                            'Oops ! Identifiant ou mot de passe incorrect. Veuillez vérifier vos informations de connexion !'
                        );

                        $field->addError($error);
                        break;
                    case !$result->getAccountIsActivate() && !is_null($result->getAccountIsActivate()):
                        $field = $form->get('name');
                        $error = new FormError(
                            'L\'accès à votre compte est en attente d\'activation. Pour toute demande, notre support est à votre disposition.'
                        );
                        $field->addError($error);
                        break;
                    case !$result->getPasswordCorrect() && !is_null($result->getPasswordCorrect()):
                        $field = $form->get('password');
                        $error = new FormError('Oops ! Il semblerait que le mot de passe soit incorrect !');
                        $field->addError($error);
                        break;
                }
            }
        }
        $parameters["form"] = $form;

        return new Response($this->render("sign_in.twig", $parameters), CodeStatus::CLIENT);
    }

    #[Route(path: '/logout', name: 'logout', methods: ["GET"])]
    public function logout(Request $request): ?RedirectResponse
    {
        $userConnected = $request->getSession()->get('user_connected');
        if (!$userConnected) {
            $this->createNotFoundException();
        }
        $request->getSession()->clear();

        return $this->redirectToRoute('homepage');
    }

    #[Route(path: '/reset-password/', name: 'send_reset_password_link', methods: ["POST"])]
    public function sendPasswordResetLink(
        MailerInterface $mailer,
        Request $request,
        UserRepository $userRepository,
    ): Response {
        $form = $this->createForm(ForgotPassword::class);
        $form->handleRequest($request);
        $code = CodeStatus::CLIENT;
        $token = $request->request->all()["forgot_password"]["_token"];

        if ($form->isValid() && $form->isSubmitted() && $this->isCsrfTokenValid("reset_password_with_link", $token)) {
            $user = new User();
            $user->setName($form->getData()->getName());
            $userFound = current($userRepository->findBy(["name" => $user->getName()]));

            if ($userFound) {
                $this->setToken();
                $token = $request->getSession()->get("token");;
                $request->getSession()->set("ask_reset_password", true);

                $email = (new Email())
                    ->from("snowtricks@gmail.com")
                    ->to($userFound->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->text(
                        body: "Bonjour " . $user->getName() . ",\n
Nous avons bien reçu votre demande de réinitialisation de mot de passe pour votre compte. Voici les étapes à suivre :\n

Cliquez sur le lien ci-dessous pour accéder à la page de réinitialisation : \n
http://127.0.0.1:8000/reset-password/token/$token\n

Sur la page, saisissez un nouveau mot de passe pour votre compte. \n

Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer ce message. N'hésitez pas à nous contacter si vous avez des questions. \n

Cordialement,\n

L'équipe Snowtricks
"
                    );
                $mailer->send($email);
                $parameters["mail_send"] = 1;
                $parameters["token"] = $token;
                $code = CodeStatus::REQUEST_SUCCEED;
            } else {
                $error = new FormError('Oops ! Identifiant incorrect. Veuillez vérifier vos informations !');
                $form->get('name')->addError($error);
            }
        }

        $userConnected = $request->getSession()->get('user_connected');
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $parameters["form"] = $form;
        return new Response($this->render("forgot_password.twig", $parameters), $code);
    }


    #[Route(path: '/reset-password/token/{id}', name: 'reset_password_with_token', methods: ["POST"])]
    public function resetPassword(
        Request $request,
        string $id,
        UserRepository $userRepository
    ): Response {
        $resetPasswordkAsk = $request->getSession()->get('ask_reset_password');
        $form = $this->createForm(ResetPassword::class);
        $form->handleRequest($request);
        $token = $request->request->all()['reset_password']['_token'];
        if ($form->isValid() && $form->isSubmitted() && $this->isCsrfTokenValid(
                "reset_password",
                $token
            ) && $resetPasswordkAsk) {
            $response = new Response();
            $user = new User();
            $user->setName($form->getData()->getName());
            $user->setOldPassword($form->getData()->getOldPassword());
            $user->setPassword($form->getData()->getPassword());
            $userEntity = $userRepository->checkPasswordReset($user);
            switch (true) {
                case $userEntity->getCredentialsValid():
                    $response->headers->clearCookie("token");
                    $response->send();
                    $request->getSession()->remove('ask_reset_password');
                    return $this->redirectToRoute("homepage");
                case !$user->getPasswordCorrect() && !is_null($user->getPasswordCorrect()):
                    $error = new FormError('Oops ! Il semble que le mot de passe saisi est incorrect !');
                    $form->get('oldPassword')->addError($error);
                    break;
                default:
                    $error = new FormError(
                        'Oops ! Identifiant ou mot de passe incorrect. Veuillez vérifier vos informations de connexion !'
                    );
                    $form->get('name')->addError($error);
            }
        }
        $userConnected = $request->getSession()->get('user_connected');
        $parameters["token"] = $id;
        $parameters["form"] = $form;
        $parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render("reset_password.twig", $parameters), CodeStatus::CLIENT);
    }

}

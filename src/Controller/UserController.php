<?php

namespace App\Controller;

use App\Entity\User;
use App\Enumeration\UserStatus;
use App\Enumeration\CodeStatus;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{


    public string $template = "";
    /**
     * @var array <string,int>
     */
    public array $parameters = [];
    public ?int $code = null;

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route(path: '/signup', methods: ["GET"])]
    public function signUpPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $this->template = "sign_up.twig";
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render($this->template, $this->parameters));
    }

    #[Route(path: '/signin', methods: ["GET"])]
    public function signInPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $this->template = "sign_in.twig";
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render($this->template, $this->parameters));
    }

    #[Route(path: '/forgot-password', methods: ["GET"])]
    public function forgotPasswordPage(Request $request): Response
    {
        $userConnected = $request->getSession()->get('user_connected');
        $this->template = "forgot_password.twig";
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render($this->template, $this->parameters));
    }

    #[Route(path: '/reset-password/token/{id}', methods: ["GET"])]
    public function resetPasswordPage(Request $request, string $id = null): Response|RedirectResponse
    {
        $tokenInSession = $request->getSession()->get('token');
        $userConnected = $request->getSession()->get('user_connected');
        if (!is_null($id) && $id == $tokenInSession) {
            $this->template = "reset_password.twig";
            $this->parameters["token"] = $id;
            $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
            return new Response($this->render($this->template, $this->parameters));
        }
        throw $this->createNotFoundException();
    }


    #[Route(path: '/signup/registration', methods: ['POST'])]
    public function signUpValidator(
        ValidatorInterface $validator,
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer,
    ): ?Response {
        $this->template = "sign_up.twig";
        $user = new User();
        $numberOfErrors = 0;
        $result = "";
        $user->setName($request->request->get("username"));
        $user->setEmail($request->request->get("email"));
        $user->setPassword($request->request->get("password"));
        $file = $request->files->all()['file'];

        if (!is_null($file)) {
            $user->setFile($file);
        }

        $groups = [
            "username_exception",
            "email_exception",
            "file_exception",
            "password_exception"
        ];

        $groupsViolations = [];

        foreach ($groups as $group) {
            $errors = $validator->validate($user, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }

        if ($numberOfErrors == 0) {
            $passwordFromForm = $request->request->get('password');
            $user->setPassword(password_hash($passwordFromForm, PASSWORD_DEFAULT));
            $result = $userRepository->createUser($user);
            if (is_null($result)) {
                $user->setCreated(true);
                $this->sendMailToUser($mailer, $user);
                return $this->redirectToRoute("homepage");
            }
        }
        $userConnected = $request->getSession()->get('user_connected');
        $this->parameters["message_db"] = $result;
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $this->parameters["exceptions"] = $groupsViolations;
        $this->code = CodeStatus::CLIENT;
        return new Response($this->render($this->template, $this->parameters), $this->code);
    }


    public function getToken(Request $request): ?string
    {
        $cookie = $request->cookies->get("token");
        if (!empty($cookie)) {
            return $cookie;
        }
        return null;
    }

    public function setToken(): void
    {
        $session = new Session();
        if (!$session->isStarted()) {
            $response = new Response();
            $response->headers->setCookie(new Cookie("token", bin2hex(random_bytes(20))));
            $session->set("token", current($response->headers->getCookies())->getValue());
            $response->send();
        }
    }

    public function setSessionData(string $name, mixed $value): void
    {
        $session = new Session();
        if (!$session->isStarted()) {
            $session->set($name, $value);
        }
    }

    public function destroySessionData(string $name): void
    {
        $session = new Session();
        if (!$session->isStarted()) {
            $session->remove($name);
        }
    }


    public function sendMailToUser(MailerInterface $mailer, User $user): void
    {
        if ($user->isCreated()) {
            $this->setToken();
            $this->setSessionData("username", $user->getName());
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
    public function checkToken(Request $request): ?Response
    {
        $cookie = $this->getToken($request);
        $tokenInSession = $request->getSession()->get('token');
        $response = new Response();
        $this->template = "account_validation.twig";
        if (!is_null($cookie) && $cookie == $tokenInSession) {
            $userStatusUpdated = $this->userRepository->updateUserStatus($request);
            if ($userStatusUpdated) {
                $this->destroySessionData("token");
                $response->headers->clearCookie("token");
                $response->send();
                $this->parameters["token_authenticated"] = 1;
                return new Response(
                    $this->render(
                        $this
                            ->template,
                        $this->parameters
                    )
                );
            }
        }
        $userConnected = $request->getSession()->get('user_connected');
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $this->code = CodeStatus::CLIENT;
        return new Response(
            $this->render(
                $this
                    ->template,
                $this->parameters
            ), $this->code
        );
    }

    #[Route(path: '/signin/validation', methods: ["POST"])]
    public function signInValidator(
        ValidatorInterface $validator,
        Request $request,
        UserRepository $userRepository
    ): ?Response {
        $this->template = "sign_in.twig";
        $this->parameters = [];
        $user = new User();
        $numberOfErrors = 0;
        $user->setName($request->request->get("username"));
        $user->setPassword($request->request->get("password"));
        $groups = [
            "username_exception_sign_in",
            "password_exception_sign_in"
        ];
        $groupsViolations = [];
        foreach ($groups as $group) {
            $errors = $validator->validate($user, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }
        $result = $userRepository->login($user, $request);

        if ($numberOfErrors == 0) {
            switch (true) {
                case array_key_exists("password_failed", $result) || array_key_exists("username_failed", $result):
                    $this->parameters["message_db"] = $result;
                    break;
                case  array_key_exists("connected", $result):
                    $this->setSessionData("user_id", $result["user_id"]);
                    $this->setSessionData("user_connected", $result["connected"]);
                    $this->setSessionData("profile_image", $result["profile_image"]);
                    $this->setToken();
                    return $this->redirectToRoute('homepage');

                case array_key_exists("not_activate", $result):
                    $this->parameters["not_activate"] = "L'accès à votre compte est en attente d'activation. Pour toute demande, notre support est à votre disposition.";
                    break;
            }
        }
        $this->parameters["exceptions"] = $groupsViolations;
        $this->code = CodeStatus::CLIENT;
        return new Response($this->render($this->template, $this->parameters), $this->code);
    }

    #[Route(path: '/logout', methods: ["GET"])]
    public function logout(Request $request): ?RedirectResponse
    {
        $this->template = "homepage.twig";
        $userConnected = $request->getSession()->get('user_connected');
        if (!$userConnected) {
            $this->createNotFoundException();
        }
        $request->getSession()->clear();

        return $this->redirectToRoute('homepage');

    }

    #[Route(path: '/reset-password/', methods: ["POST"])]
    public function sendPasswordResetLink(
        MailerInterface $mailer,
        Request $request,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ): Response {
        $this->template = "forgot_password.twig";
        $user = new User();
        $user->setName($request->request->get('username'));
        $numberOfErrors = 0;
        $errorUsername = "";
        $groups = [
            "username_exception_forgot_password",
        ];
        $groupsViolations = [];
        foreach ($groups as $group) {
            $errors = $validator->validate($user, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }

        if ($numberOfErrors == 0) {
            $result = $userRepository->checkUser($user);
            if ($result) {
                $this->setToken();
                $token = $request->getSession()->get("token");;
                $this->setSessionData("ask_reset_password", UserStatus::ASK_RESET_PASSWORD);

                $email = (new Email())
                    ->from("snowtricks@gmail.com")
                    ->to($result->getEmail())
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
                $this->parameters["mail_send"] = 1;
                $this->parameters["token"] = $token;
                $this->code = CodeStatus::REQUEST_SUCCEED;
            } else {
                $errorUsername = "Oops! Le nom d'utilisateur " . $user->getName() . " n'existe pas !";
                $this->code = CodeStatus::CLIENT;
                $this->parameters["errorUsername"] = $errorUsername;
            }
        } else {
            $this->parameters["exceptions"] = $groupsViolations;
        }

        $userConnected = $request->getSession()->get('user_connected');
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        return new Response($this->render($this->template, $this->parameters), $this->code);
    }


    #[Route(path: '/reset-password/token/{id}', methods: ["POST"])]
    public function resetPassword(
        Request $request,
        string $id,
        ValidatorInterface $validator,
        UserRepository $userRepository
    ): Response {
        $groupsViolations = [];
        $resetPasswordkAsk = $request->getSession()->get('ask_reset_password');
        if ($resetPasswordkAsk) {
            $this->template = "reset_password.twig";
            $numberOfErrors = 0;
            $response = new Response();
            $user = new User();
            $user->setName($request->request->get("username"));
            $user->setOldPassword($request->request->get("old-password"));
            $user->setPassword($request->request->get("password"));

            $groups = [
                "username_exception_reset_password",
                "password_exception_old_reset_password",
                "password_exception_new_reset_password",
                "password_exception_wrong_format"
            ];
            foreach ($groups as $group) {
                $errors = $validator->validate($user, null, $group);
                if (count($errors) >= 1) {
                    $numberOfErrors++;
                }
                foreach ($errors as $error) {
                    $groupsViolations[$group] = $error->getMessage();
                }
            }

            if ($numberOfErrors == 0) {
                $result = $userRepository->checkPasswordReset($user);
                switch (true) {
                    case is_null($result):
                        $response->headers->clearCookie("token");
                        $response->send();
                        $this->destroySessionData('ask_reset_password');
                        return $this->redirectToRoute("homepage");
                    case array_key_exists("password", $result) || array_key_exists("username", $result) :
                        $this->code = CodeStatus::CLIENT;
                        $this->parameters["failed"] = $result;
                }
            } else {
                $this->code = CodeStatus::CLIENT;
            }
        }
        $userConnected = $request->getSession()->get('user_connected');
        $this->parameters["token"] = $id;
        $this->parameters["user_connected"] = !empty($userConnected) ? $userConnected : '';
        $this->parameters["exceptions"] = $groupsViolations;
        return new Response($this->render($this->template, $this->parameters), $this->code);
    }

}

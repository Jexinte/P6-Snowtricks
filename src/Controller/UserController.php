<?php
namespace App\Controller;

use App\Controller\DTO\UserDTO;
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


    public string $template = "sign_up.twig";

    public  function __construct(private readonly UserRepository $userRepository){}
    #[Route(path:'/signup',methods: ["GET"])]
    public function signUpPage():Response
    {
        return new Response($this->render($this->template,[
            "user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
        ]));
    }
    #[Route(path:'/signin',methods: ["GET"])]
    public function signInPage():Response
    {
        $this->template = "sign_in.twig";
        return new Response($this->render($this->template,[
            "user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
        ]));
    }   #[Route(path:'/forgot-password',methods: ["GET"])]
    public function forgotPasswordPage():Response
    {
        $this->template = "forgot_password.twig";
        return new Response($this->render($this->template,[
            "user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
        ]));
    }
    #[Route(path:'/reset-password/token/{id}',methods:["GET"])]

    public  function resetPasswordPage(Request $request, string $id):Response
    {
        $this->template = "reset_password.twig";
        return new Response($this->render($this->template,[
            "token" => $id,
            "user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
        ]));
    }

    #[Route(path:'/signup/registration', methods : ['POST'])]
    public function signUpValidator(ValidatorInterface $validator,Request $request,UserRepository $userRepository,MailerInterface $mailer):?Response
    {
        $userDto = new UserDTO();
        $numberOfErrors = 0;
        $userDto->setName($request->request->get("username"));
        $userDto->setEmail($request->request->get("email"));
        $userDto->setPassword($request->request->get("password"));
        $file = $request->files->all()['file'];
        if(!is_null($file)){
            $userDto->setFile($file);
        }

        $groups = [
        "username_exception",
            "email_exception",
            "file_exception",
            "password_exception"
        ];

        $groupsViolations = [];
        foreach($groups as  $group){
            $errors = $validator->validate($userDto,null,$group);
            if(count($errors) >= 1){
                $numberOfErrors++;
            }
            foreach($errors as $error){
                $groupsViolations[$group] = $error->getMessage();
            }
        }
        if($numberOfErrors == 0)
       {
           $passwordFromForm = $request->request->get('password');
            $userDto->setPassword(password_hash($passwordFromForm,PASSWORD_DEFAULT));
            $result = $userRepository->createUser($userDto);
            if(is_array($result)){
                return new Response($this->render($this->template,[
                    "message_db" => $result,
                    !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
                ]),400);

            }
            $userDto->setCreated(true);
            $this->sendMailToUser($mailer,$userDto,$request);
       }
        return $userDto->isCreated() ? new RedirectResponse("/"):new Response($this->render($this->template,[
            "exceptions" => $groupsViolations,
            !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
        ]),400);
    }


    public function getCookie(Request $request):?string
    {
        $cookie = $request->cookies->get("token");
        if(!empty($cookie)){
            return $cookie ;
        }
        return null;
    }

   public function setToken():void
    {
        $session = new Session();
        if(!$session->isStarted()){
            $response = new Response();
            $response->headers->setCookie(new Cookie("token",bin2hex(random_bytes(20))));
            $response->send();
            $session->set("token",current($response->headers->getCookies())->getValue());
        }

    }

    public function setSessionData($name,$value):void
    {
        $session = new Session();
        if(!$session->isStarted()){
            $session->set($name,$value);

        }
    }

    public function destroySessionData($name):void
    {
        $session = new Session();
        if(!$session->isStarted()){
            $session->remove($name);
        }
    }


    public function getSessionData($name):string|int|null
    {
        $session = new Session();
        if(!$session->isStarted()){
            return $session->get($name);
        }
      return null;
    }

    public function sendMailToUser(MailerInterface $mailer,UserDTO $userDTO,Request $request):void
    {
        if($userDTO->isCreated()){
         $this->setToken();
         $request->getSession()->set("username",$userDTO->getName());
            $email = (new Email())
                ->from("snowtricks@gmail.com")
                ->to('mdembelepro@gmail.com')

                ->subject('Confirmation d\'inscription')
            ->text(
                body: "Bonjour ".$userDTO->getName().",\n
Nous vous remercions de vous être inscrit(e) sur notre plateforme. Avant de pouvoir accéder à toutes les fonctionnalités, nous vous prions de bien vouloir valider votre compte en cliquant sur le lien ci-dessous :\n 
http://127.0.0.1:8000/signup/account-validation\n
Si vous ne pouvez pas cliquer sur le lien, veuillez copier et coller l'URL dans la barre d'adresse de votre navigateur.\n
L'équipe Snowtricks
");
            $mailer->send($email);
        }


    }

    #[Route(path:'/signup/account-validation',methods: ["GET"])]
    public function checkToken(Request $request):?Response
    {
        $session = $request->getSession();
        $cookie = $this->getCookie($request);
        $response = new Response();
        $this->template ="account_validation.twig";
        if(!is_null($cookie) && $cookie == $session->get('token')){
            $userStatusUpdated = $this->userRepository->updateUserStatus($request);
            if ($userStatusUpdated){
                $session->remove('token');
                $response->headers->clearCookie("token");
                $response->send();
            return new Response($this->render($this
            ->template,["token_authenticated" => 1]),200);
            }
        }

        return new Response($this->render($this
            ->template,[
            "user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
        ]),400);
    }

    #[Route(path:'/signin/validation',methods:["POST"])]
    public function signInValidator(ValidatorInterface $validator,Request $request,UserRepository $userRepository) :?Response
    {
        $this->template = "sign_in.twig";
        $userDto = new UserDTO();
        $numberOfErrors = 0;
        $userDto->setName($request->request->get("username"));
        $userDto->setPassword($request->request->get("password"));
        $groups = [
            "username_exception_sign_in",
            "password_exception_sign_in"
        ];
        $groupsViolations = [];
        foreach ($groups as $group) {
            $errors = $validator->validate($userDto, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }
        $result = $userRepository->login($userDto,$request);

        if ($numberOfErrors == 0) {
                switch (true){
                    case is_array($result) && array_key_exists("password_failed",$result) || array_key_exists("username_failed",$result):
                        return new Response(
                            $this->render($this->template, [
                                "message_db" => $result,
                            ]), 400
                        );
                    case is_array($result) && array_key_exists("connected",$result):
                        $this->setSessionData("user_id",$result["user_id"]);
                        $this->setSessionData("user_connected",$result["connected"]);
                        return new RedirectResponse("/");
                }

        }
        return  new Response(
            $this->render($this->template, [
                "exceptions" => $groupsViolations,
            ]), 400
        );
    }

    #[Route(path:'/logout',methods:["GET"])]
    public function logout():?Response
    {
        $this->template = "homepage.twig";
        if(!$this->getSessionData("user_connected"))
        {
            return null;
        }
        $this->destroySessionData("user_connected");
        return new Response($this->render($this->template));
    }

    #[Route(path:'/reset-password/',methods:["POST"])]
    public function sendPasswordResetLink(MailerInterface $mailer,Request $request,UserDTO $userDto,UserRepository $userRepository,ValidatorInterface $validator):Response
    {
        $this->template = "forgot_password.twig";
        $userDto->setName($request->request->get('username'));
        $numberOfErrors = 0;
        $errorUsername = "";
        $groups = [
            "username_exception_forgot_password",
        ];
        $groupsViolations = [];
        foreach ($groups as $group) {
            $errors = $validator->validate($userDto, null, $group);
            if (count($errors) >= 1) {
                $numberOfErrors++;
            }
            foreach ($errors as $error) {
                $groupsViolations[$group] = $error->getMessage();
            }
        }

        if ($numberOfErrors == 0) {
            $result = $userRepository->checkUser($userDto);
            if($result)
            {
                $this->setToken();
                $token = $request->getSession()->get("token");


                $email = (new Email())
                    ->from("snowtricks@gmail.com")
                    ->to('mdembelepro@gmail.com')

                    ->subject('Réinitialisation de votre mot de passe')
                    ->text(
                        body: "Bonjour ".$userDto->getName().",\n
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
                return new Response($this->render($this->template,[
                    "mail_send" => 1,
                    "token" => $token

                ]),200);
            } else{
                $errorUsername = "Oops! Le nom d'utilisateur ".$userDto->getName()." n'existe pas !";
            }

        }


        return new Response($this->render($this->template,[
            "errorUsername" => $errorUsername,
            "exceptions" => $groupsViolations,
            "user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
            ]),400);
    }



#[Route(path:'/reset-password/token/{id}',methods:["POST"])]

public  function resetPassword(Request $request,string $id,UserDTO $userDto,ValidatorInterface $validator,UserRepository $userRepository):Response
{
    $this->template = "reset_password.twig";
    $numberOfErrors = 0;
    $response = new Response();

    $userDto->setName($request->request->get("username"));
    $userDto->setOldPassword($request->request->get("old-password"));
    $userDto->setPassword($request->request->get("password"));

    $groups = [
        "username_exception_reset_password",
        "password_exception_old_reset_password",
"password_exception_new_reset_password",
        "password_exception_wrong_format"
    ];
    $groupsViolations = [];
    foreach ($groups as $group) {
        $errors = $validator->validate($userDto, null, $group);
        if (count($errors) >= 1) {
            $numberOfErrors++;
        }
        foreach ($errors as $error) {
            $groupsViolations[$group] = $error->getMessage();
        }
    }

    if ($numberOfErrors == 0) {
        $result = $userRepository->checkPasswordReset($userDto);
        if ($result) {
            $response->headers->clearCookie("token");
            $response->send();
            return new RedirectResponse("/",302);
        }
    }
    return new Response($this->render($this->template, [
        "token" => $id,
        "exceptions" => $groupsViolations,
        "user_connected" => !empty($this->getSessionData("user_connected")) ? $this->getSessionData("user_connected") : ''
        ]),400);
}

}

<?php
namespace App\Controller;

use App\Controller\DTO\UserDTO;
use App\Entity\User;
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
        return new Response($this->render($this->template));
    }
    #[Route(path:'/signin',methods: ["GET"])]
    public function signInPage():Response
    {
        $this->template = "sign_in.twig";
        return new Response($this->render($this->template));
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
                    "message_db" => $result
                ]),400);

            }
            $userDto->setCreated(true);
            $this->sendMailToUser($mailer,$userDto,$request);
       }
        return $userDto->isCreated() ? new RedirectResponse("/"):new Response($this->render($this->template,[
            "exceptions" => $groupsViolations,
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
        $session->start();
        $response = new Response();
        $response->headers->setCookie(new Cookie("token",bin2hex(random_bytes(20))));
        $response->send();
        $session->set("token",current($response->headers->getCookies())->getValue());
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
            ->template),400);
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


            if (is_array($result)) {
                return new Response(
                    $this->render($this->template, [
                        "message_db" => $result
                   ]), 400
               );
            }

        }
        return !is_array($result) ? new RedirectResponse("/") : new Response(
            $this->render($this->template, [
                "exceptions" => $groupsViolations,
            ]), 400
        );
    }

}

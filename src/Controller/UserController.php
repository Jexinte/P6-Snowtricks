<?php
namespace App\Controller;

use App\Controller\DTO\UserDTO;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\Session;

$session = new Session();
$session->start();
class UserController extends AbstractController
{

    public string $template = "sign_up.twig";

    public  function __construct(private readonly UserRepository $userRepository){}
    #[Route('/signup')]
    public function signUpPage():Response
    {
        return new Response($this->render($this->template));
    }


    #[Route('/signup/registration')]
    public function signUpValidator(ValidatorInterface $validator,Request $request,UserRepository $userRepository,EntityManagerInterface $entityManager,MailerInterface $mailer):?Response
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
            $userDto->setPassword(password_hash($request->request->get('password'),PASSWORD_DEFAULT));
            $result = $userRepository->createUser($userDto,$entityManager);
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


    public function getCookie(Request $request):string
    {
            return $request->cookies->get("PHPSESSID") ;
    }
    public function sendMailToUser(MailerInterface $mailer,UserDTO $userDTO,Request $request):void
    {
        if($userDTO->isCreated()){
            $request->getSession()->set("PHPSESSID",$this->getCookie($request));
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

    #[Route('/signup/account-validation')]
    public function token(Request $request):?Response
    {

        if($this->getCookie($request) == $request->getSession()->get('PHPSESSID')){
            $userStatusUpdated = $this->userRepository->updateUserStatus($request);
            if ($userStatusUpdated){
                $this->template ="account_validation.twig";
            return new Response($this->render($this
            ->template));
            }
        }

        return null;

    }

}

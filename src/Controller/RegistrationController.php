<?php

namespace App\Controller;

use App\Entity\Skills;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Validator\Constraints\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();       
        $skills=new Skills(); 
        $user->addSkill($skills);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            // encode the password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('Password')->getData()
                )
            );
            //for uploading file
            /** @var UploadedFile $file */
            $file=$request->files->get('registration_form') ['Images'];
            //dump($request);
            //dump($user);
            $upload_directory=$this->getParameter('uploads_directory');
            $filename=md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($upload_directory,$filename);
            $user->setImage($filename);

            $entityManager->persist($user);
            $entityManager->flush();
            

             $userAuthenticator->authenticateUser(
              $user,
                $authenticator,
                $request
            );
            return $this->redirectToRoute('app_login');
        
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }


    #[Route('/registerform', name: 'registerform')]
    public function displayForm(){
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    
    #[Route('/user', name: 'user')]
    public function homepage( EntityManagerInterface $entityManager,){
        $user = new User();

        $entities = $entityManager->getRepository(User::class)->findAll();

        // $Form = $this->createForm(UserType::class, $user);/
          return $this->render('homepage/home.html.twig',[

              'results'=>$entities,
          ]);
    }
}

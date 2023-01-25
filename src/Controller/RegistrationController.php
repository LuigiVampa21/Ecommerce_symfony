<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
                            Request $request, 
                            UserPasswordHasherInterface $userPasswordHasher, 
                            UserAuthenticatorInterface $userAuthenticator, 
                            UsersAuthenticator $authenticator, 
                            EntityManagerInterface $entityManager,
                            SendMailService $mail,
                            JWTService $jwt,
                            EntityManagerInterface $em
                            ): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));

            $mail->send(
                'no-reply@adelfos.com',
                $user->getEmail(),
                'Email verification',
                'register',
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

        /**
         * @Route("/verif/{token}", name="verify_user")
         */
    public function verifyUser($token, 
                                JWTService $jwt, 
                                UsersRepository $usersRepository, 
                                EntityManagerInterface $em
                                ): Response
    {
        if($jwt->isValid($token) &&
          !$jwt->isExpired($token) && 
           $jwt->check($token, $this->getParameter('app.jwtsecret')))
           {
                $payload = $jwt->getPayload($token);

                $user = $usersRepository->find($payload['user_id']);

                if($user && !$user->getIsVerified()){
                    $user->setIsVerified(true);
                    $em->flush($user);
                    $this->addFlash('success', 'User activated');
                    return $this->redirectToRoute('app_profile_index');
                }
            }
        $this->addFlash('danger', 'Token is invalid or expired');
        return $this->redirectToRoute('app_login');
    }


            /**
         * @Route("/resendverif", name="resend_verif")
         */
        public function resendVerif(
                                    SendMailService $mail, 
                                    JWTService $jwt, 
                                    UsersRepository $usersRepository 
                                    )
        {
            $user = $this->getUser();
            if(!$user){
                $this->addFlash('danger', 'You need to be connected to acceess this route.');
                return $this->redirectToRoute('app_login');    
            }

            if($user->getIsVerified()){
                $this->addFlash('warning', 'User already actived');
                return $this->redirectToRoute('app_profile_index');    
            }
            
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            
            $payload = [
            'user_id' => $user->getId()
            ];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'no-reply@adelfos.com',
                $user->getEmail(),
                'Email verification',
                'register',
                compact('user', 'token')
            );
            $this->addFlash('success', 'Verification email sent');
            return $this->redirectToRoute('app_profile_index');
    }

}
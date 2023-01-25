<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UsersRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

     /**
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPassword(
                                      Request $request, 
                                      UsersRepository $usersRepository,
                                      TokenGeneratorInterface $tokenGeneratorInterface,
                                      EntityManagerInterface $em,
                                      SendMailService $mail
                                  ):   Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            if($user){
                $token = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $context = compact('url', 'user');

                $mail->send(
                    'no-reply@adelfos.com',
                    $user->getEmail(),
                    'Reset Your Password',
                    'password_reset',
                    $context
                );
                $this->addFlash('success', 'Email sent successfully');
                return $this->redirectToRoute(('app_login'));
            };
            $this->addFlash('danger', 'An error occured');
            return $this->redirectToRoute(('app_login'));
        }
        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

     /**
     * @Route("/forgot-password/{token}", name="reset_password")
     */
    public function resetPass(
                            string $token,
                            Request $request,
                            UsersRepository $usersRepository,
                            EntityManagerInterface $em,
                            UserPasswordHasherInterface $passwordHasher
                            ): Response
    {
        $user = $usersRepository->findOneByResetToken($token); 
        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user->setResetToken("");
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Password updated successfully');
                return $this->redirectToRoute(('app_login'));
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        };
        $this->addFlash('danger', 'Invalid token');
        return $this->redirectToRoute(('app_login'));
    }
}

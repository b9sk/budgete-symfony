<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
    
    /**
     * @Route("/login/reset", name="reset_passwd")
     */
    public function resetPassword(Request $request, MailerInterface $mailer): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }
        
        $user = new User();
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        
        $isError = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $repository = $this->getDoctrine()->getRepository(User::class);
            $dbUser = $repository->findOneBy(['email' => $email]);
            
            // if given email is not found in User entity items
            if (!$dbUser) {
                // do nothing but error message
                $this->addFlash(
                    'danger',
                    'A user with this email is not found.'
                );
                // and suggestion to register instead
                $isError = true;
            }
            else {
                // generate a recovery password token for the user
                $token = md5(random_bytes(10));
                $dbUser->setRecoveryToken($token);
                
                // compose an url with the token
                $tokenLink = $this->generateUrl('login_new_password', ['recovery_token' => $token], 0);
    
                // put the token to db
                $em = $this->getDoctrine()->getManager();
                $em->persist($dbUser);
                $em->flush();
    
                // compose an email message
                $email = ( new Email() )
                    ->from('budgete@b9sk.ru')
                    ->to('i@b9sk.ru')
                    ->subject('Password recovery')
                    // @todo: use a twig template
                    ->text('Click the link for recovery your password: ' . $tokenLink);
                
                // send the message
                $mailer->send($email);
    
                // redirect the user to /login/reset/done
                return $this->redirectToRoute('reset_passwd_success');
    
            }
        }
        
        return $this->render('security/reset_passwd.html.twig', [
            'form' => $form->createView(),
            'title' => 'Reset your password',
            'is_error' => $isError
        ]);
    
    }
    
    /**
     * @Route("/login/reset/done", name="reset_passwd_success")
     */
    public function passwordResetIsDone(Request $request): Response
    {
        return $this->render('security/reset_passwd_done.html.twig', [
            'title' => 'Check your email',
        ]);
    }
    
    /**
     * @Route("/login/new-password", name="login_new_password")
     */
    public function newPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $token = $request->get('recovery_token');
    
        // find a user by given token in db
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(['recoveryToken' => $token]);

        $isTokenFound = true;
        if (!$user) {
            $isTokenFound = false;
        }
    
        // render a form for new password
        $form = $this->createFormBuilder()
            ->add('newPassword', PasswordType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 6,
                        'max' => 128,
                    ]),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Set new password',
            ])
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('newPassword')->getData();
    
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $password)
            );
            
            // todo: remove the token from db
            $user->setRecoveryToken(null);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Your password has been updated. Login using your new password.');
            
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/new_password.html.twig', [
            'title' => 'Type a new password',
            'is_token_found' => $isTokenFound,
            'form' => $form->createView(),
        ]);
    }
}

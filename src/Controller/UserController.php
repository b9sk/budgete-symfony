<?php

namespace App\Controller;

use App\Form\UserFormType;
use App\Form\UserPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    
    /**
     * User Profile Settings Page
     *
     * @Route("/user", name="user")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted(['ROLE_USER']);
        // clear a post data after saving the form in order to prevent
        // an unwanted form submit when the page has been reloaded by F5
        $redirect = $this->redirect($this->generateUrl('user'));
        
        $user = $this->getUser();
        
        // profile settings form
        $profileForm = $this->createForm(UserFormType::class, $user);
        $profileForm->handleRequest($request);
        
        if ( $profileForm->isSubmitted() && $profileForm->isValid() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            $this->addFlash('success', 'Your profile has been saved.');
            
            return $redirect;
        }
    
        // change password form
        $passwordForm = $this->createForm(UserPasswordType::class, $user);
        $passwordForm->handleRequest($request);
        
        if ( $passwordForm->isSubmitted() && $passwordForm->isValid() ) {
            $oldPassword = $passwordForm->get('oldPassword')->getData();
            $newPassword = $passwordForm->get('newPassword')->getData();
            
            if ($passwordEncoder->isPasswordValid($user, $oldPassword)) {
                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $newPassword)
                );
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->addFlash('success', 'Your Password has been updated.');
                
                return $redirect;
            }
            else {
                $this->addFlash('danger', 'You typed wrong old password.');
            }
        }
        
        return $this->render('user/index.html.twig', [
            'profile_form' => $profileForm->createView(),
            'password_form' => $passwordForm->createView(),
            'user' => $user,
        ]);
    }
}

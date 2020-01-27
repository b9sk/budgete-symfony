<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(UrlGeneratorInterface $urlGenerator)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        // redirect a user if he comes to /dashboard
        return new RedirectResponse($urlGenerator->generate('dashboard_user', ['user_id' => $user->getId()]));
    }
    
    /**
     * @Route("/dashboard/{user_id}", name="dashboard_user")
     */
    public function userDashboard($user_id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
        ]);
    }
}

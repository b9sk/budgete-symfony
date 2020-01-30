<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Budget;


class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index($limit = 12)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
    
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $this->getUser(),
        ]);
    }
    
}

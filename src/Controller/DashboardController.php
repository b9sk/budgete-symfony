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
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $this->getUser();
    
        // fetch recent budget data of the user
        $budget_repository = $this->getDoctrine()->getRepository(Budget::class);
        $budget_result = $budget_repository->findBy(['user' => $user->getId()], ['created' => 'DESC'], 12);
    
    
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
            'recent' => $budget_result
        ]);
    }
    
}

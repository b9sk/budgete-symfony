<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

// @todo: implement the routes
//   - /page/# - paginator
//   - /add - create a budget record that belongs to its user
class BudgetController extends AbstractController
{
    /**
     * @Route("/dasboard/budget", name="budget")
     */
    public function index()
    {
        return $this->render('budget/index.html.twig', [
            'controller_name' => 'BudgetController',
        ]);
    }
    
    /**
     * @Route("/dasboard/budget/add", name="budget_add")
     */
    public function add()
    {
        return $this->render('budget/index.html.twig', [
            'controller_name' => 'BudgetController',
        ]);
    }
}

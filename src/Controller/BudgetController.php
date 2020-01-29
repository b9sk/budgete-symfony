<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Entity\User;
use App\Form\BudgetFormType;
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
        $budget = new Budget();
        $user = new User();
        
        $budget->setUser($user);
        $budget->setCreated(new \DateTimeImmutable('now'));
        
        $form = $this->createForm(BudgetFormType::class, $budget);
        
        return $this->render('budget/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

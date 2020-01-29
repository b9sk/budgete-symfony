<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

// @todo: implement the routes
//   - /page/# - paginator
//   - /add - create a budget record that belongs to its user
class BudgetController extends AbstractController
{
    /**
     * @Route("/dashboard/budget", name="budget")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
    
        return $this->render('budget/index.html.twig', [
            'controller_name' => 'BudgetController',
            'user' => $this->getUser(),
        ]);
    }
    
    /**
     * @Route("/dashboard/budget/add", name="budget_add")
     */
    public function add(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
    
        $budget = new Budget();
        // set a data before form rendered
        $budget->setUser($this->getUser());
        $budget->setCreated(new \DateTime('now'));
        $form = $this->createForm(BudgetFormType::class, $budget);
    
        // performs when the form submitted
        $form->handleRequest($request);
        
        // performs validating
        if ($form->isSubmitted() && $form->isValid()) {
            
            // if the form is valid push a data to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($budget);
            $em->flush();
    
            // push a message to twig when succeeded
            $this->addFlash(
                'success',
                'The record was saved.'
            );
        
        }
        
        
        return $this->render('budget/add.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }
}

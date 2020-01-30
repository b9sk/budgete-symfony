<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetFormType;
use App\Form\Type\DateTimePickerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @Route("/dashboard/budget/add", name="add_budget")
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
            
            return $this->redirect($this->generateUrl('dashboard'));
        
        }
        
        
        return $this->render('budget/form.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'title' => 'Add expense or income',
        ]);
    }
    
    /**
     * @Route("/dashboard/budget/edit/{id}", name="edit_budget")
     */
    public function budgetById($id, Request $request)
    {
        // get a budget record by id
        $budget_item = $this->getDoctrine()->getRepository(Budget::class)->find($id);
    
        // access resolver
        $this->denyAccessUnlessGranted('own', $budget_item);

        // reuse BudgetFormType form
        $form = $this->createForm(BudgetFormType::class, $budget_item);
        
        // disabled due an unexpected bug in Chrome built-in datapicker for Android 6
        // $form = $form->add('created', DateTimePickerType::class)// this moves submit button at the end of edit form ->remove('submit')->add('submit', SubmitType::class);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
        
            // if the form is valid push a data to DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($budget_item);
            $em->flush();
        
            $this->addFlash(
                'success',
                'The record was updated.'
            );
        
            return $this->redirect($this->generateUrl('dashboard'));
        }

        return $this->render('budget/form.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser(),
            'title' => 'Edit a record',
        ]);
    
    }
}

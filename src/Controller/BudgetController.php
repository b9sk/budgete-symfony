<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetFormType;
use App\Service\DateIntervalResolverService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function today(DateIntervalResolverService $datetimeResolver)
    {
        $user = $this->getUser();
        $budgetRepo = $this->getDoctrine()->getRepository(Budget::class);
        $today = $budgetRepo->getTodayRecords($user->getId());
        
        return $this->render('budget/_day.html.twig', [
            'recent' => $today,
            'title' => 'Today',
            'user' => $user,
        ]);
    }
    
    public function lastWeek()
    {
        $user = $this->getUser();
        $budgetRepo = $this->getDoctrine()->getRepository(Budget::class);
        $lastWeek = $budgetRepo->getLastWeekRecords($user->getId());
        
        dump($lastWeek);
        // @todo: week template
        return $this->render('budget/_day.html.twig', [
            'recent' => $lastWeek,
            'title' => 'Last week',
            'user' => $user,
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
    
    /**
     * @Route("/dashboard/budget/remove/{id}", name="remove_budget")
     */
    public function removeBudgetItem($id)
    {
        $budget_item = $this->getDoctrine()->getRepository(Budget::class)->find($id);
        $this->denyAccessUnlessGranted('own', $budget_item);
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($budget_item);
        $em->flush();
    
        $this->addFlash(
            'success',
            'The record was removed.'
        );
        
        return $this->redirect($this->generateUrl('dashboard'));
    }
}

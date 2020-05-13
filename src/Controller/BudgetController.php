<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetFormType;
use App\Utils\DateIntervalResolver;
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
    
    public function today()
    {
        $user = $this->getUser();
        $budgetRepo = $this->getDoctrine()->getRepository(Budget::class);
        $today = $budgetRepo->getTodayRecords($user->getId());
    
        $incomeSum = $budgetRepo->getTodaySum($user->getId(), 'income')[0];
        $expenseSum = $budgetRepo->getTodaySum($user->getId(), 'expense')[0];
        
        return $this->render('budget/_day.html.twig', [
            'recent' => $today,
            'title' => 'Today',
            'user' => $user,
            'stats' => [
                'income' => $incomeSum ?: 0,
                'expense' => $expenseSum ?: 0,
            ]
        ]);
    }
    
    public function lastWeek()
    {
        $user = $this->getUser();
        
        // aggregate required data from DB
        $repository = $this->getDoctrine()->getRepository(Budget::class);
        $recordsDbResult = $repository->getLastWeekRecords($user->getId());
        $incomeSum = $repository->getLastWeekSum($user->getId(), 'income')[0];
        $expenseSum = $repository->getLastWeekSum($user->getId(), 'expense')[0];
        
    
        //// DB response post processing
        // resort the db response and add a \Datetime property
        $records = array();
        foreach ($recordsDbResult as $item) {
            $records[$item['date']]['datetime'] = new \DateTime($item['date']);
            $records[$item['date']]['data'][] = $item;
        }

        return $this->render('budget/_week.html.twig', [
            'records' => $records,
            'title' => 'Last week',
            'user' => $user,
            'stats' => [
                'income' => $incomeSum ?: 0,
                'expense' => $expenseSum ?: 0,
            ]
        ]);
    }
    
    public function lastMonth(DateIntervalResolver $intervalResolver)
    {
        $user = $this->getUser();
    
        // aggregate required data from DB
        $repository = $this->getDoctrine()->getRepository(Budget::class);
        $recordsDbResult = $repository->getLastMonthRecords($user->getId());
        $incomeSum = $repository->getLastMonthSum($user->getId(), 'income')[0];
        $expenseSum = $repository->getLastMonthSum($user->getId(), 'expense')[0];
    
        //// DB response post processing
        // resort the db response and add a \Datetime property
        $records = array();
        foreach ($recordsDbResult as $item) {
            $records[$item['date']]['datetime'] = new \DateTime($item['date']);
            $records[$item['date']]['data'][] = $item;
        }
    
        // making additional properties those required by template
        foreach ( $records as $date => &$record ) {
            // count difference and create two properties per record
            $diff = null;
            // if given data of the record has both income ane expense records
            if (count($records[$date]['data']) > 1) {
                $diff = (int)$records[$date]['data'][1]['sum'] - (int)$records[$date]['data'][0]['sum'];
            }
            else {
                if ($records[$date]['data'][0]['type'] === 'expense') {
                    $diff = 0 - (int)$records[$date]['data'][0]['sum'];
                } elseif ($records[$date]['data'][0]['type'] === 'income') {
                    $diff = (int)$records[$date]['data'][0]['sum'];
                }
            }
        
            // add diff property, define balance of the record
            if ($diff) {
                $record['diff'] = $diff;
            
                if ($diff < 0) {
                    $record['diff_type'] = 'negative';
                } elseif ($diff > 0) {
                    $record['diff_type'] = 'positive';
                }
                else {
                    $record['diff_type'] = 'neutral';
                }
            }
        }
    
        return $this->render('budget/_month.html.twig', [
            'records' => $records,
            'title' => 'Last month',
            'user' => $user,
            'stats' => [
                'income' => $incomeSum ?: 0,
                'expense' => $expenseSum ?: 0,
            ]
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
            'currency' => $this->getUser()->getCurrency(),
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
            'budget' => $budget_item,
            'currency' => $this->getUser()->getCurrency(),
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

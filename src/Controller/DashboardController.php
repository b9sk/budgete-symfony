<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Budget;


class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard/{date}", name="dashboard", defaults={"date"=null})
     */
    public function index($date): Response
    {
        $today = (new \DateTime())->format('Y-m-d');
//        dump($today, $date);
        $date = $today === $date ? null : $date;
        $this->denyAccessUnlessGranted('ROLE_USER');
    
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $this->getUser(),
            'date' => $date,
        ]);
    }



}

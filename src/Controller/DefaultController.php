<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 24.01.20
 * Time: 18:48
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    function index()
    {
        return $this->render('default/index.html.twig');
    }
    
}
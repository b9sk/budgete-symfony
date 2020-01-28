<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 24.01.20
 * Time: 18:48
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends AbstractController
{
    function index()
    {
        // if user is logged in then redirect to dashboard
        //   - else return render of static index.html
        if ( $this->getUser() && array_search("ROLE_USER", $this->getUser()->getRoles()) !== false ) {
            return $this->redirect($this->generateUrl('dashboard'));
        }
        else {
            return $this->render('default/index.html.twig');
        }
    }
}
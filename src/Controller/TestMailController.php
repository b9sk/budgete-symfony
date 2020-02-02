<?php
// src/Controller/MailerController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    
    /**
     * @Route("/email/test", name="test_email")
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(MailerInterface $mailer)
    {
        $email = ( new Email() )
            ->from('budgete@b9sk.ru')
            ->to('i@b9sk.ru')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');
        
        /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
        $mailer->send($email);
        
        return $this->redirectToRoute('dashboard');

    }
}
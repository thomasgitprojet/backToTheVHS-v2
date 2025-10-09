<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestMailController extends AbstractController
{
    #[Route('/test-mail', name: 'app_test_mail')]
    public function sendTestMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('test@backtothevhs.fr')
            ->to('recruteur@example.com')
            ->subject('Test Mailtrap - BackToTheVHS')
            ->text('Ceci est un test d’envoi via Mailtrap.')
            ->html('<p><strong>Bravo !</strong> Si vous lisez ceci, Mailtrap fonctionne 🎉</p>');

        $mailer->send($email);

        return new Response('✅ E-mail de test envoyé via Mailtrap !');
    }
}
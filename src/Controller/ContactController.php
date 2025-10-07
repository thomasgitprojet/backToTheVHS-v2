<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use GuzzleHttp\Client;
use App\Form\ContactType;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérifier le reCAPTCHA
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            $client = new Client();
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $this->getParameter('recaptcha_secret_key'),
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->getClientIp(),
                ],
            ]);

            $result = json_decode($response->getBody(), true);

            if (empty($recaptchaResponse) || !$result['success']) {
                $this->addFlash('error', 'Veuillez confirmer que vous n\'êtes pas un robot.');
                return $this->redirectToRoute('app_contact');
            }

            // Récupération des données du formulaire
            $data = $form->getData();

            // Construction de l'email
            $email = (new Email())
                ->from($data['email'])
                ->to('ton.email@gmail.com') // 👉 remplace par ton adresse
                ->subject('[BackToTheVHS] ' . $data['subject'])
                ->text(
                    "Nom : {$data['name']}\nEmail : {$data['email']}\n\nMessage :\n{$data['message']}"
                );

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé 📬');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig', [
            'contactForm' => $form->createView(),
            'recaptcha_site_key' => $this->getParameter('recaptcha_site_key'),
        ]);
    }
}
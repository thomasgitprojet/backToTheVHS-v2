<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

            // Anti-spam : max 1 message par minute
            $session = $request->getSession();
            $lastMessageTime = $session->get('last_message_time');

            if ($lastMessageTime && (time() - $lastMessageTime < 60)) {
                $this->addFlash('error', 'Veuillez patienter une minute avant de renvoyer un message.');
                return $this->redirectToRoute('app_contact');
            }

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
                ->from('ea8d0ca08d8bb7@sandbox.mailtrap.io') // ton adresse "from"
                ->replyTo($data['email'])         // replyTo = email de l'utilisateur
                ->to('thomasdesse.pro@gmail.com')       // ton adresse pour recevoir le message
                ->subject('[BackToTheVHS] ' . $data['subject'])
                ->html("
                    <p><strong>Nom :</strong> {$data['name']}</p>
                    <p><strong>Email :</strong> {$data['email']}</p>
                    <p><strong>Sujet :</strong> {$data['subject']}</p>
                    <p><strong>Message :</strong><br>{$data['message']}</p>
                ");

            $mailer->send($email);

            // Enregistrement du dernier envoi
            $session->set('last_message_time', time());

            $this->addFlash('success', 'Votre message a bien été envoyé 📬');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig', [
            'contactForm' => $form->createView(),
            'recaptcha_site_key' => $this->getParameter('recaptcha_site_key'),
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use GuzzleHttp\Client;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginAuthenticator;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
        UserAuthenticatorInterface $authenticator,
        LoginAuthenticator $loginAuthenticator
    ): Response {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             // Vérifier reCAPTCHA
            $recaptchaResponse = $request->request->get('g-recaptcha-response');

            if (empty($recaptchaResponse)) {
                $this->addFlash('error', 'Veuillez confirmer que vous n\'êtes pas un robot.');
                return $this->redirectToRoute('app_register');
            }

            $client = new Client();
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $this->getParameter('recaptcha_secret_key'),
                    'response' => $recaptchaResponse,
                    'remoteip' => $request->getClientIp(),
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            if (!($result['success'] ?? false)) {
                $this->addFlash('error', 'Veuillez confirmer que vous n\'êtes pas un robot.');
                return $this->redirectToRoute('app_register');
            }

            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $hasher->hashPassword($account, $plainPassword);
            $account->setPassword($hashedPassword);

            $account->setRoles(['ROLE_USER']);
            $entityManager->persist($account);
            $entityManager->flush();

             return $authenticator->authenticateUser(
                 $account,
                 $loginAuthenticator,
                 $request
             );
        }

        return $this->render('register/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
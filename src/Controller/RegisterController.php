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
// use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
// use App\Security\LoginAuthenticator;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher,
        // UserAuthenticatorInterface $authenticator,
        // LoginAuthenticator $loginAuthenticator
    ): Response {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $hasher->hashPassword($account, $plainPassword);
            $account->setPassword($hashedPassword);

            $entityManager->persist($account);
            $entityManager->flush();

            // On supprime la connexion automatique pour le moment
            // return $authenticator->authenticateUser(
            //     $account,
            //     $loginAuthenticator,
            //     $request
            // );

            // Rediriger vers la page de connexion ou autre
            return $this->redirectToRoute('home');
        }

        return $this->render('register/register.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
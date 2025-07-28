<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AccountController extends AbstractController
{
    #[Route('/monCompte', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function index(UserInterface $user): Response
    {
        return $this->render('account/account.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/monCompte/modifier', name: 'app_account_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserInterface $user,
        UserPasswordHasherInterface $hasher
    ): Response {
        $form = $this->createForm(AccountType::class, $user, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ici, on ne touche pas au mot de passe (puisque plainPassword n'est pas dans le form)

            $em->flush();

            $this->addFlash('success', 'Vos informations ont bien été mises à jour.');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

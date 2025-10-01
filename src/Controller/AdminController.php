<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\UserAdminType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(Account::class)->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}', name: 'app_admin_user_show', requirements: ['id' => '\d+'])]
    public function show(Account $user): Response
    {
        return $this->render('admin/user_show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_admin_user_edit')]
    public function edit(Account $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été mis à jour.");
            return $this->redirectToRoute('app_admin_user_show', ['id' => $user->getId()]);
        }

        return $this->render('admin/user_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, Account $user, EntityManagerInterface $em): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete_user_'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Le jeton CSRF est invalide.');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/publication/{id}/delete', name: 'app_admin_publication_delete', methods: ['POST'])]
    public function deletePublication(
        int $id,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $publication = $em->getRepository(\App\Entity\Publication::class)->find($id);

        if (!$publication) {
            $this->addFlash('error', 'Publication introuvable.');
            return $this->redirectToRoute('app_admin_dashboard');
        }

        // Vérif CSRF
        if ($this->isCsrfTokenValid('delete_publication' . $publication->getId(), $request->request->get('_token'))) {
            $em->remove($publication);
            $em->flush();

            $this->addFlash('success', 'Publication supprimée avec succès ✅');
        } else {
            $this->addFlash('error', 'Token CSRF invalide ❌');
        }

        return $this->redirectToRoute('app_admin_dashboard');
    }
}

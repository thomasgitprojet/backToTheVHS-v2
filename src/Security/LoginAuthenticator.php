<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public function __construct(private UrlGeneratorInterface $urlGenerator) {}

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Si c’est une requête AJAX, on renvoie JSON
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'message' => 'Connexion réussie !',
                'type' => 'success',
            ]);
        }

        // Sinon, redirection classique
        $request->getSession()->getFlashBag()->add('success', 'Connexion réussie ! Bienvenue 👋');

        // Vérifie si un paramètre redirect est présent dans l'URL
        $redirect = $request->request->get('redirect', $request->query->get('redirect'));
        if (is_string($redirect) && $this->isSafeRedirect($redirect)) {
            return new RedirectResponse($redirect);
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_connection');
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        // Méthode 1 : détection Symfony
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'message' => 'Non connecté (AJAX)',
            ], 401);
        }

        // Méthode 2 : detection JSON
        if (str_contains($request->headers->get('Accept', ''), 'application/json')) {
            return new JsonResponse([
                'message' => 'Non connecté (JSON)',
            ], 401);
        }

        // Sinon redirection classique
        return new RedirectResponse($this->getLoginUrl($request));
    }

    /**
     * N'autorise que les chemins internes au site (ex. "/publication/6").
     */
    private function isSafeRedirect(string $url): bool
    {
        return str_starts_with($url, '/')
            && !str_starts_with($url, '//')
            && !str_starts_with($url, '/\\');
    }
}
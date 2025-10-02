<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginAuthenticator extends AbstractAuthenticator
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        // On supporte uniquement le formulaire de login sur la route 'app_login'
        return $request->attributes->get('_route') === 'app_login'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');
        $password = $request->request->get('password', '');

        return new Passport(
            new UserBadge($username, function (string $userIdentifier) use ($password) {
                // Ici, tu dois récupérer l’utilisateur depuis la base de données
                // et vérifier le mot de passe. Exemple :
                // return $userRepository->findOneByUsername($userIdentifier);
            }),
            // Si tu veux valider le mot de passe, ajoute un Credential ici
            // Exemple: new PasswordCredentials($password)
            [],
            [
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        // Redirige l’utilisateur vers la page d’accueil après login
        $targetUrl = $this->urlGenerator->generate('home');
        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Redirige vers la page de login avec une erreur
        $request->getSession()->set('login_error', $exception->getMessage());
        $targetUrl = $this->urlGenerator->generate('app_login');
        return new RedirectResponse($targetUrl);
    }
}
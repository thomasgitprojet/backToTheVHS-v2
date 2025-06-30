<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class LoginSuccessListener
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onLoginSuccess(InteractiveLoginEvent $event): void
    {
        $session = $this->requestStack->getSession();
        if ($session) {
            $session->getFlashBag()->add('success', 'Connexion réussie');
        }
    }
}
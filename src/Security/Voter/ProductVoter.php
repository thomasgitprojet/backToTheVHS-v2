<?php

namespace App\Security\Voter;

use App\Entity\Account;
use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Seul le compte qui a publié un produit peut le modifier ou le supprimer.
 *
 * @extends Voter<string, Product>
 */
final class ProductVoter extends Voter
{
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE], true)
            && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        // Visiteur non connecté : refus
        if (!$user instanceof Account) {
            return false;
        }

        /** @var Product $subject */
        return $subject->getPublication()?->getAccount() === $user;
    }
}
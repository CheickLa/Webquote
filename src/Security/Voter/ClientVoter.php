<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientVoter extends Voter
{
    public const CREATE = 'CREATE';
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!in_array('ROLE_USER', $user->getRoles())) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                return true;
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $user->getAgency() === $subject->getAgency();
        }

        return false;
    }
}

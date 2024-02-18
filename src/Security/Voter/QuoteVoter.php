<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class QuoteVoter extends Voter
{
    public const CREATE = 'CREATE';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const VIEW = 'VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof \App\Entity\Quote;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (!in_array('ROLE_USER', $user->getRoles())) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
            case self::VIEW:
              $matchClientAgency = $user->getAgency() === $subject->getClient()->getAgency();
              $services = $subject->getServices();
              $matchEachService = array_map(fn($service) => $user->getAgency() === $service->getServiceCategory()->getAgency(), $services->toArray());
              return $matchClientAgency && in_array(false, $matchEachService) === false;
        }

        return false;
    }
}

<?php

namespace App\Security\Voter;

use App\Entity\Budget;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BudgetVoter extends Voter
{
    protected const OWN = 'own';
    
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::OWN])
            && $subject instanceof Budget;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::OWN:
                // logic to determine if the user can EDIT
                // return true or false
                return $user->getId() == $subject->getUser()->getId();
                break;
        }

        return false;
    }
}

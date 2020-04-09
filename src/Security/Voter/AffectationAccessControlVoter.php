<?php

namespace App\Security\Voter;

use App\Entity\Affectation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AffectationAccessControlVoter extends Voter
{
    protected function supports($attribute, $subject)
    {

        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['POST', 'VIEW'])
            && $subject instanceof Affectation;
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
            case 'POST':
               if($user->getRoles() == ['ROLE_PARTENAIRE'] || $user->getRoles() == ['ROLE_ADMIN_PARTENAIRE']){
                   return true;
               }
               if($user->getPartenaire()->getId() == $subject->getUser()->getPartenaire()->getId() && $subject->getCompte()->getPartenaire()->getId()){
                   return true;
               }

                break;
            case 'VIEW':
                if($user->getRoles() == ['ROLE_PARTENAIRE'] || $user->getRoles() == ['ROLE_ADMIN_PARTENAIRE']){
                    return true;
                }
                break;
        }

        return false;
    }
}

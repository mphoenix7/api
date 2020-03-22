<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAccessVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['POST', 'GET'])
            && $subject instanceof User;
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
                if($user->getRoles() ==[ 'ROLE_ADMIN_SYSTEM' ] && $subject->getProfil()->getLibelle() != 'ROLE_ADMIN_SYSTEM' ){
                    return true;
                }
                if($user->getRoles() == ['ROLE_ADMIN'] && ($subject->getProfil()->getLibelle() != 'ADMIN_SYSTEM' &&
                        $subject->getProfil()->getLibelle() != 'ADMIN')){
                    return true;
                }
                if($user->getRoles() == ['ROLE_PARTENAIRE'] && $subject->getProfil()->getLibelle() == 'ADMIN_PARTENAIRE'
                    && $subject->getProfil()->getLibelle() == 'CAISSIER_PARTENAIRE' ){
                    return true;
                }
                if($user->getRoles() == ['ROLE_ADMIN_PARTENAIRE'] && $subject->getProfil()->getLibelle() == 'CAISSIER_PARTENAIRE' ){
                    return true;
                }
                break;
            case 'POST_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}

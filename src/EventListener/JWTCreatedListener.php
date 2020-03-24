<?php


namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\Exception\DisabledException;


class JWTCreatedListener
{
//
    public  function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if($user->getIsActif() == 0){
            throw new DisabledException();

        }

       elseif($user->getPartenaire() != Null && $user->getPartenaire()->getUser()->get(0)->getIsActif() == 0){
            throw new DisabledException();
        }

    }

}
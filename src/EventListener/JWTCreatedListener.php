<?php


namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\DisabledException;


class JWTCreatedListener
{
//
    public  function onJWTCreated(JWTCreatedEvent $event){
        $user = $event->getUser();
        if($user->getIsActif() == false){
            throw new DisabledException();

        }
    }

}
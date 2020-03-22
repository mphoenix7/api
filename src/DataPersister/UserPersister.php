<?php
namespace App\DataPersister ;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;


class UserPersister implements ContextAwareDataPersisterInterface{
    public function __construct( Security $security , UserPasswordEncoderInterface $encoder , EntityManagerInterface $em , TokenStorageInterface $token)
    {
        $this->security = $security;
        $this->encoder = $encoder ;
        $this->em = $em;
        $this->token = $token;
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User ;
    }


    public function persist($data, array $context = [])
    {

           $user = new User();

           $data->setPassword($this->encoder->encodePassword($user , $data->getPassword()))
               ->setRoles(['ROLE_'.$data->getProfil()->getLibelle()]);
           $this->em->persist($data);
           $this->em->flush();

    }
    public function remove($data, array $context = [])
    {
         $this->em->remove($data);
         $this->em->flush();
    }
}
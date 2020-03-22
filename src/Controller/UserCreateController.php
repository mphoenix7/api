<?php 

namespace App\Controller ;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateController
{
    public function __construct(EntityManagerInterface $em , UserPasswordEncoderInterface $encoder , Security $security){
        $this->em = $em;
        $this->encoder = $encoder;
        $this->security = $security;
    }

     

    public function __invoke(User $data)
    {
        //dd($data);
        
       if($this->security->isGranted('POST' , $data)){
         $data->setUsername($data->getUsername())
         ->setPassword($this->encoder->encodePassword($data , $data->getPassword()))
         ->setProfil($data->getProfil())
         ->setRoles(['ROLE_'.$data->getProfil()->getLibelle()])
         ->setIsActif($data->getIsActif());
         $this->em->flush();
          return $data;
       
       }
       else {
           $json = ['statu' => 403,
            'message' =>"Vous n'êtes pas autorisés à effectuer cette action !"];
            return new JsonResponse($json,403);
       }

      
     }
}
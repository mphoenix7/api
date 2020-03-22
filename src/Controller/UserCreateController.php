<?php 

namespace App\Controller ;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateController
{
    public function __construct(ValidatorInterface $validator ,EntityManagerInterface $em , UserPasswordEncoderInterface $encoder , Security $security, UserRepository $user){
        $this->em = $em;
        $this->encoder = $encoder;
        $this->security = $security;
        $this->user = $user;
        $this->validator = $validator;
    }

     

    public function __invoke(User $data)
    {
        $check = $data->getUsername();
        $foundusername = $this->user->findOneBy(['username' => $check]);

       if($this->security->isGranted('POST' , $data)){
             if(is_null($foundusername) == true ){
                 $data->setUsername($data->getUsername())
                     ->setPassword($this->encoder->encodePassword($data , $data->getPassword()))
                     ->setProfil($data->getProfil())
                     ->setRoles(['ROLE_'.$data->getProfil()->getLibelle()])
                     ->setIsActif($data->getIsActif());
                 $errors = $this->validator->validate($data);
                 if (count($errors) > 0){
                    return new JsonResponse($errors , 400);
                 }
                 $this->em->flush();


                 return $data ;
             }
             else {
                 $json = ["message" => "Ce nom d'utilisateur existe déja veuillez choisir un autre nom"];
                 return new JsonResponse($json);
             }
       
       }
       else {
           $json = ['statu' => 403,
            'message' =>"Vous n'êtes pas autorisés à effectuer cette action !"];
            return new JsonResponse($json,403);
       }

      
     }
}
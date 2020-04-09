<?php

namespace App\Controller;

use Cassandra\Date;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Affectation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

class AffectationController
{
    private $em , $security , $validator;
    public function __construct(Security $security, EntityManagerInterface $em , ValidatorInterface $validator)
    {
        $this->security = $security;
        $this->em = $em;
        $this->validator = $validator;
    }

    public function __invoke(Affectation $data)
    {

        $dateDebut = strtotime($data->getDateDebut()->format('d-m-Y'));
        $dateFin = strtotime($data->getDateFin()->format('d-m-Y'));
        $date = strtotime(Date('d-m-Y'));

        if ($this->security->isGranted("POST", $data))
        {
            $affectations = $this->em->getRepository(Affectation::class)->findAll();

            foreach ($affectations as $affectation){


                if($affectation->getDateDebut()->format('d-m-Y') == $data->getDateDebut()->format('d-m-Y') && $affectation->getDateFin()->format('d-m-Y') == $data->getDateFin()->format('d-m-Y'))
                {
                    $json = ['message' => "L'utilisateur a déja une affectation entre le ".$affectation->getDateDebut()->format('d-m-Y')." et le ".$affectation->getDateFin()->format('d-m-Y')."."];
                    return new JsonResponse($json);
                }
                elseif ($dateDebut < $date || $dateFin < $date)
                {
                    $json = ["message" => "Mauvaise Date!"];
                    return new JsonResponse($json);
                }
                elseif ($dateDebut == $dateFin){
                    $json = ["message" => "Une affectation dure au moins 24H"];
                    return new JsonResponse($json);
                }

            }
            $errors = $this->validator->validate($data);
            if (count($errors) > 0){
                return new JsonResponse($errors, 400);
            }
            $this->em->flush();
            return $data ;

        }
        else {
            $json = ["statu" => 403, "message" => "Vous n'êtes pas autorisés à effectuer cette action"];
            return new JsonResponse($json, 403);
        }


    }
}

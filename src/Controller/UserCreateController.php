<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserCreateController
{
    /**
     * @var ValidatorInterface
     */

    private $em, $encoder, $security, $validator;

    public function __construct(ValidatorInterface $validator, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Security $security)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->security = $security;
       // $this->user = $user;
        $this->validator = $validator;
    }


    public function __invoke(User $data)
    {
        //dd($data);

        if (!$this->security->isGranted('POST', $data)) {
            $json = ["message" => "Vous ne pouvez pas effectuer cette action !"];
            return new JsonResponse($json, 403);
        } else {
            $data->setPassword($this->encoder->encodePassword($data, $data->getPassword()));
            $data->setRoles(['ROLE_' . $data->getProfil()->getLibelle()]);
            $errors = $this->validator->validate($data);

            if (count($errors) > 0) {
                return new JsonResponse($errors, 400);
            }
            $this->em->flush();
            return $data;

        }


    }
}
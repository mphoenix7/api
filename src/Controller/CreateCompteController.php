<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Depot;
use App\Entity\Partenaire;
use App\Entity\User;
use App\Repository\PartenaireRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Security;


class CreateCompteController extends AbstractController
{
    public function __construct(UserPasswordEncoderInterface $encoder, TokenStorageInterface $token)
    {
        $this->encoder = $encoder;
        $this->token = $token;
    }

    /**
     * @Route("/api/createcompte", name="create_compte")
     *
     */
    public function createCompte( Security $security, ValidatorInterface $validator , Request $request, SerializerInterface $serializer, PartenaireRepository $partenaireRepository, UserRepository $userRepository, EntityManagerInterface $em)
    {

        $json = $request->getContent();
        $data = $serializer->deserialize($json, Compte::class, 'json');


        if (isset($data)) {
            //dd($data);
            $value = $data->getPartenaire()->getNinea();
            $foundninea = $partenaireRepository->findOneBy(['ninea' => $value]);
            if (is_null($foundninea) == true) {
                $partenaire = new Partenaire();
                $partenaire->setPrenom($data->getPartenaire()->getPrenom())
                    ->setNom($data->getPartenaire()->getNom())
                    ->setEmail($data->getPartenaire()->getEmail())
                    ->setAdresse($data->getPartenaire()->getAdresse())
                    ->setTelephone($data->getPartenaire()->getTelephone())
                    ->setNinea($data->getPartenaire()->getNinea())
                    ->setRegistreCom($data->getPartenaire()->getRegistreCom());
                $errors = $validator->validate($partenaire);
                if (count($errors) > 0){
                    return new JsonResponse($errors , 400);
                }
                $em->persist($partenaire);

                $foundusername = $userRepository->findOneBy(['username' => $data->getPartenaire()->getUser()->get(0)->getUsername()]);
                if (is_null($foundusername) == true) {


                        $user = new User();
                        $user->setUsername($data->getPartenaire()->getUser()->get(0)->getUsername())
                            ->setPassword($this->encoder->encodePassword($user, $data->getPartenaire()->getUser()->get(0)->getPassword()))
                            ->setIsActif($data->getPartenaire()->getUser()->get(0)->getIsactif())
                            ->setRoles(['ROLE_' . $data->getPartenaire()->getUser()->get(0)->getProfil()->getLibelle()])
                            ->setProfil($data->getPartenaire()->getUser()->get(0)->getProfil());
                    if($security->isGranted('POST', $user)) {
                            $user->setPartenaire($partenaire);
                        $errors = $validator->validate($user);
                        if (count($errors) > 0) {
                            return new JsonResponse($errors, 400);
                        }
                        $em->persist($user);
                    }
                    else {
                        $json = ['statu' => 403,
                            'message' => "Vous ne pouvez pas effectuer cette action !"];
                        return new JsonResponse($json, 403);
                    }


                    $compte = new Compte();
                    $compte->setNumeroCompte((uniqid()))
                        ->setDateCreation(new \DateTime())
                        ->setSolde(0)
                        ->setUserCreator($this->token->getToken()->getUser())
                        ->setPartenaire($partenaire);
                    $errors = $validator->validate($compte);
                    if (count($errors) > 0){
                        return new JsonResponse($errors , 400);
                    }
                    $em->persist($compte);

                    if ($data->getDepots()->get(0)->getMontant() >= 500000) {

                        $depot = new Depot();
                        $depot->setMontant($data->getDepots()->get(0)->getMontant())
                            ->setDateDepot(new \DateTime())
                            ->setUserwhodid($this->token->getToken()->getUser())
                            ->setCompte($compte);
                        $errors = $validator->validate($depot);
                        if (count($errors) > 0){
                            return new JsonResponse($errors , 400);
                        }
                        $em->persist($depot);

                        $compte->setSolde($compte->getSolde() + $data->getDepots()->get(0)->getMontant());
                        $em->persist($compte);
                        $em->flush();
                    } else {
                        $data = ["message" => "500000 minimum pour le depot"];

                        return new JsonResponse($data);
                    }

                } else {
                    $data = ['message' => "Ce nom d'utilisateur existe déja !"];
                    return new JsonResponse($data);
                }
                $data = [
                    'statu' => 201,
                    'message' => "Le compte Partenaire a bien été crée"
                ];
                return new JsonResponse($data, 201);
            } else {
                $compte = new Compte();
                $compte->setNumeroCompte((uniqid()))
                    ->setSolde(0)
                    ->setDateCreation(new \DateTime())
                    ->setSolde(0)
                    ->setPartenaire($partenaireRepository->findOneBy(['ninea' => $value]))
                    ->setUserCreator($this->token->getToken()->getUser());
                $em->persist($compte);

                if ($data->getDepots()->get(0)->getMontant() >= 500000) {

                    $depot = new Depot();
                    $depot->setMontant($data->getDepots()->get(0)->getMontant())
                        ->setDateDepot(new \DateTime())
                        ->setUserwhodid($this->token->getToken()->getUser())
                        ->setCompte($compte);
                    $em->persist($depot);

                    $compte->setSolde($compte->getSolde() + $data->getDepots()->get(0)->getMontant());
                    $em->persist($compte);
                    $em->flush();
                } else {
                    $data = ["message" => "Le montant de dépot doit être au minimum 500000"];

                    return new JsonResponse($data);
                }
                $data = ['statu' => 201, 'message' => 'Nouveau compte crée pour ce partenaire'];
                return new JsonResponse($data, 201);
            }

        }

    }
}

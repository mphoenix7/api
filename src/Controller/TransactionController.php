<?php

namespace App\Controller;


use App\Entity\Tarif;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransactionController
{
    private $em, $security, $token, $validator, $repository;

    public function __construct(TransactionRepository $repository, EntityManagerInterface $em, Security $security, TokenStorageInterface $token, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->security = $security;
        $this->token = $token;
        $this->validator = $validator;
        $this->repository = $repository;

    }


    public function tarif($frais)
    {
        $check_tarif = $this->em->getRepository(Tarif::class)->findAll();
        foreach ($check_tarif as $tarif) {
            if ($frais >= $tarif->getBorneInf() && $frais <= $tarif->getBorneSup()) {
                $frais = $tarif->getValeur();
            }
        }
        return $frais;
    }

    public function __invoke(Transaction $data)
    {

        if ($this->security->isGranted('POST', $data)) {

            $user = $this->token->getToken()->getUser();
            if ($user->getRoles() == ['ROLE_CAISSIER_PARTENAIRE']) {
                $date = strtotime(Date('d-m-Y'));
                //dd($date);
                $currentaffect = $user->getAffectations();
                foreach ($currentaffect as $affect) {
                    if (strtotime($affect->getDateFin()->format('d-m-Y')) > $date && strtotime($affect->getDateDebut()->format('d-m-Y')) < $date) {
                        $compte = $affect->getCompte();
                        // dd($compte);

                    }


                }
                if (!isset($compte)) {
                    $json = ["message" => "Aucune affectation encours veuillez contacter votre Administrateur"];
                    return new JsonResponse($json, 404);
                }


                if ($compte->getSolde() < $data->getMontant()) {
                    $json = ["Votre Solde est insuffisant"];
                    return new JsonResponse($json, 400);
                } elseif ($data->getMontant() > $compte->getSolde()) {
                    $json = ["Vous ne pouvez pas effectuer un dépot pour le moment"];
                    return new JsonResponse($json, 400);
                } else {
                    $data->setCode(uniqid('PT-'))
                        ->setFrais($this->tarif($data->getMontant()))
                        ->setCompteD($compte)
                        ->setUserD($this->token->getToken()->getUser())
                        ->setCommissionEmetteur(($data->getFrais() * 10) / 100)
                        ->setDateEnvoie(new \DateTime())
                        ->setCommissionSysteme(($data->getFrais() * 30) / 100)
                        ->setCommissionRecepteur(($data->getFrais() * 20) / 100)
                        ->setStatu(1)
                        ->setTaxeEtats(($data->getFrais() * 40) / 100);
                    $errors = $this->validator->validate($data);
                    if (count($errors) > 0) {
                        return new JsonResponse($errors, 400);
                    }
                    $this->em->flush();

                    return $data;
                }
            } else {
                if ($data->getCompteD()->getSolde() < $data->getMontant()) {
                    $json = ["Votre Solde est insuffisant"];
                    return new JsonResponse($json, 400);
                } else {
                    $data->setCode(uniqid('PT-'))
                        ->setFrais($this->tarif($data->getMontant()))
                        ->setUserD($this->token->getToken()->getUser())
                        ->setCommissionEmetteur(($data->getFrais() * 10) / 100)
                        ->setDateEnvoie(new \DateTime())
                        ->setCommissionSysteme(($data->getFrais() * 30) / 100)
                        ->setCommissionRecepteur(($data->getFrais() * 20) / 100)
                        ->setStatu(1)
                        ->setTaxeEtats(($data->getFrais() * 40) / 100);
                    $errors = $this->validator->validate($data);
                    if (count($errors) > 0) {
                        return new JsonResponse($errors, 400);
                    }
                    $this->em->flush();

                    return $data;
                }

            }


        } else {
            $json = ["message" => "Vous n'êtes pas autorisés à effectuer cette action !"];
            return new JsonResponse($json, 403);
        }
    }
}

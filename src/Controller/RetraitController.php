<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RetraitController extends AbstractController
{
    /**
     * @Route("/api/transactions/retrait/{code}", name="transaction_retrait" , methods={"PUT"})
     */
    public function TransactionRetrait(Request $request, $code, TransactionRepository $repo, EntityManagerInterface $manager,
                                       TokenStorageInterface $token, SerializerInterface $serializer)
    {
        $check_transaction = $repo->findOneByCode($code);
        $json = $request->getContent();
        $data = $serializer->deserialize($json, Transaction::class, 'json');
        $test = new \DateTime();
        //dd($test);
        $transaction = new Transaction();

        if ($this->isGranted('POST', $transaction)) {
            $user = $token->getToken()->getUser();
            if (is_null($check_transaction) == false && $check_transaction->getStatu() == true) {
                if ($user->getRoles() == ['ROLE_CAISSIER_PARTENAIRE']) {
                    $date = strtotime(Date('d-m-Y'));
                    $currentaffect = $user->getAffectations();

                    foreach ($currentaffect as $affect) {

                        if ($date < strtotime($affect->getDateFin()->format('d-m-Y')) && $date >= strtotime($affect->getDateDebut()->format('d-m-Y'))) {
                            //$data->setCompteR($affect->getCompte());
                            $compte = $affect->getCompte();


                        }


                    }

                    if ($compte->getSolde() - $check_transaction->getMontant() > 0 || $compte->getsolde() > $check_transaction->getMontant()) {
                        $check_transaction->setDateRetrait($test)
                            ->setCompteR($compte)
                            ->setRecepteur($check_transaction->getRecepteur())
                            ->setUserR($user)
                            ->setTypePieceRecepteur($data->getTypePieceRecepteur())
                            ->setNumeroPieceRecepteur($data->getNumeroPieceRecepteur());

                        $check_transaction->getCompteD()->setSolde($check_transaction->getCompteD()->getSolde() + $check_transaction->getMontant());
                        $check_transaction->getCompteR()->setSolde($check_transaction->getCompteR()->getSolde() - $check_transaction->getMontant());
                        $check_transaction->setStatu(false);
                        $manager->persist($check_transaction);
                        $manager->flush();
                    } else {
                        return new JsonResponse("Votre solde est insuffisant. Solde: " . $compte->getSolde());
                    }
                } else {
                        if ($compte->getSolde() - $check_transaction->getMontant() > 0 || $compte->getsolde() > $check_transaction->getMontant()) {
                            $check_transaction->setDateRetrait($test)
                                ->setCompteR($compte)
                                ->setRecepteur($check_transaction->getRecepteur())
                                ->setCompteR($data->getCompteR())
                                ->setUserR($user)
                                ->setTypePieceRecepteur($data->getTypePieceRecepteur())
                                ->setNumeroPieceRecepteur($data->getNumeroPieceRecepteur());

                            $check_transaction->getCompteD()->setSolde($check_transaction->getCompteD()->getSolde() + $check_transaction->getMontant());
                            $check_transaction->getCompteR()->setSolde($check_transaction->getCompteR()->getSolde() - $check_transaction->getMontant());
                            $check_transaction->setStatu(false);
                            $manager->persist($check_transaction);
                            $manager->flush();
                        } else {
                            return new JsonResponse("Votre solde est insuffisant. Solde: " . $compte->getSolde());
                        }

                }
            }else{
                $json = ["message" => "Ce code n'existe pas dans la base ou la somme à déja été retiré"];
                return new JsonResponse($json , 404);

            }


        } else {
            $json = ["Message" => "Vous n'êtes pas autorisés à effectuer cette opération!"];
            return new JsonResponse($json, 403);
        }


        return new JsonResponse("Opération réussie ! " , 200);


    }
}

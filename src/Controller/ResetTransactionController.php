<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ResetTransactionController extends AbstractController
{
    /**
     * @Route("/api/transactions/reset/{code}", name="transaction_reset" , methods={"PUT"})
     */
    public function Reset(Request $request, $code, TransactionRepository $repo, EntityManagerInterface $manager, SerializerInterface $serializer)
    {
        $transaction = new Transaction();
        if ($this->isGranted('POST', $transaction)) {
            $check_transaction = $repo->findOneByCode($code);
            //dd($check_transaction);
            if (is_null($check_transaction) == false && $check_transaction->getStatu() == 1) {
                $check_transaction->getCompteD()->setSolde($check_transaction->getCompteD()->getSolde() + $check_transaction->getMontant());
                $manager->persist($check_transaction);
                $manager->flush();
                $manager->remove($check_transaction);
                $manager->flush();

                $json = ["message" => "Le transfert à été annulé"];
                return new JsonResponse($json, 200);


            } else {
                $json = ["message" => "Transaction introuvable !"];
                return new JsonResponse($json, 404);
            }
        } else {
            $json = ["message" => "Vous n'êtes pas autorisés à effectuer cette action !"];
            return new JsonResponse($json, 403);
        }

    }
}

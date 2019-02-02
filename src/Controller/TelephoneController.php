<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Telephone;

class TelephoneController extends AbstractController
{
    /**
     * @Route("/telephone/get/{id}", name="get_telephone")
     * Requête de type GET
     * @return telephone
     */
    public function getTelephone($id) {
        $repository = $this->getDoctrine()->getRepository(Telephone::class);
        $telephone = $repository->find($id);
        $response = array();
        if ($telephone !== NULL) {
            
            $response["id"] = $telephone->getId();
            $response["num"] = $telephone->getNum();
            $response["credit"] = $telephone->getCredit();
        }
        return new JsonResponse($response);
    }

    /**
     * @Route("/transaction", name="transaction")
     * Requête de type POST
     * @return boolean
     */
    public function transaction(Request $request) {
        $data = json_decode($request->getContent(), true);
        $repository = $this->getDoctrine()->getRepository(Telephone::class);
        $src = $repository->findOneBy([
            "num" => $data["src"]
        ]);
        $dest = $repository->findOneBy([
            "num" => $data["dest"]
        ]);
        if (($src === NULL) || ($dest === NULL)) {
            $result["message"] = "Source ou destinataire non existant dans la base de données";
            return new JsonResponse($result, 419);
        }
        if ($data["amount"] > $src->getCredit()) {
            $result["message"] = "Crédit insuffisant pour effectuer cette transaction";
            return new JsonResponse($result, 419);
        }
        $src->setCredit($src->getCredit() - $data["amount"]);
        $dest->setCredit($dest->getCredit() + $data["amount"]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($src);
        $entityManager->flush();
        $entityManager->persist($dest);
        $entityManager->flush();

        return new JsonResponse(true);
    }
}

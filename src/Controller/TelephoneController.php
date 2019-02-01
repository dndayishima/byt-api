<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\Request;
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
}

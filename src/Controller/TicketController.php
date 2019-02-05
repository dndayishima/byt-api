<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Ticket;

use \DateTime;

class TicketController extends AbstractController
{
    /**
     * @Route("/ticket", name="ticket")
     * Création d'un ticket
     * Requête de type POST
     */
    public function createTicket(Request $request) {

        // Attention : Est-ce qu'une même personne peut acheter un ticket 2 fois ?
        // Pour l'instant bloquer ceci côté front-end

        // Tous les champs sont obligatoires pour la création d'un ticket
        $entityManager = $this->getDoctrine()->getManager();

        // Conversion du JSON en tableau associatif PHP
        $data = json_decode($request->getContent(), true);

        $result = array();
        if (!$this->valideTicket($data)) {
            $result["message"] = "Un champ important n'est pas renseigné dans les données !";
            return new JsonResponse($result, 419);
        } else {
            $ticket = new Ticket();
            $ticket->setClient($data["client"]);
            $ticket->setVendeur($data["vendeur"]);
            $ticket->setNumClient($data["numClient"]);
            $ticket->setNumVendeur($data["numVendeur"]);
            $ticket->setPrix($data["prix"]);
            $ticket->setEvenementId($data["evenementId"]);
            $dateAchat = new DateTime();
            $ticket->setDateAchat($dateAchat);
            $dateEvenement = new DateTime($data["dateEvenement"]);
            $ticket->setDateEvenement($dateEvenement);
            $ticket->setValide(true);

            $entityManager->persist($ticket);
            $entityManager->flush();
        }

        // Après insertion d'un nouveau ticket dans la base de données,
        // retourner cet objet au complet

        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->findOneBy([
            "client" => $data["client"],
            "vendeur" => $data["vendeur"],
            "numClient" => $data["numClient"],
            "numVendeur" => $data["numVendeur"],
            "prix" => $data["prix"],
            "evenementId" => $data["evenementId"],
            "dateAchat" => $dateAchat,
            "dateEvenement" => $dateEvenement
        ]);

        if ($ticket !== NULL) {
            $result["id"] = $ticket->getId();
            $result["client"] = $ticket->getClient();
            $result["vendeur"] = $ticket->getVendeur();
            $result["numClient"] = $ticket->getNumClient();
            $result["numVendeur"] = $ticket->getNumVendeur();
            $result["prix"] = $ticket->getPrix();  
            $result["evenementId"] = $ticket->getEvenementId();
            $result["dateAchat"] = $ticket->getDateAchat()->format("Y-m-d");
            $result["dateEvenement"] = $ticket->getDateEvenement()->format("Y-m-d");
        }
        return new JsonResponse($result);
    }

    /**
     * Vérification de la validité de tous les champs
     * On vérifie s'ils sont renseigné
     * @return boolean
     */
    public function valideTicket($data) {
        return (
            isset($data["client"]) &&
            isset($data["vendeur"]) &&
            isset($data["numClient"]) &&
            isset($data["numVendeur"]) &&
            isset($data["prix"]) &&
            isset($data["evenementId"]) &&
            isset($data["dateEvenement"])
        );
    }

    /**
     * @Route("/bought", name="bought")
     * Vérifier si un ticket a été acheté par un utilisateur donné
     * Requête de type POST
     * @return boolean
     */
    public function bought(Request $request) {
        $data = json_decode($request->getContent(), true);
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $dateEvenement = new DateTime($data["dateEvenement"]);
        $ticket = $repository->findOneBy([
            "client" => $data["client"],
            "vendeur" => $data["vendeur"],
            "evenementId" => $data["evenementId"],
            "prix" => $data["prix"],
            "dateEvenement" => $dateEvenement
        ]);
        if ($ticket === NULL) {
            $response = new JsonResponse(false);
        } else {
            $response = new JsonResponse(true);
        }
        return $response;
    }

    /**
     * @Route("/findtickets/{client}", name="findtickets")
     * Requête de type GET
     * @return array tickets
     */
    public function findTickets($client) {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy([
            "client" => $client
        ]);
        $result = array();
        foreach ($tickets as $ticket) {
            $t["id"] = $ticket->getId();
            $t["client"] = $ticket->getClient();
            $t["vendeur"] = $ticket->getVendeur();
            $t["numClient"] = $ticket->getNumClient();
            $t["numVendeur"] = $ticket->getNumVendeur();
            $t["prix"] = $ticket->getPrix();
            $t["evenementId"] = $ticket->getEvenementId();
            $t["dateAchat"] = $ticket->getDateAchat()->format("Y-m-d");
            $t["dateEvenement"] = $ticket->getDateEvenement()->format("Y-m-d");
            $t["valide"] = $ticket->getValide();
            array_push($result, $t);
        }
        return new JsonResponse($result);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Evenement;

use \DateTime;

/**
 * TODO : 
 * 1) Fonction qui retourne la liste des évemenents créés par un vendeur donné
 * 2) Get evenement by ID
 * 3) Get evenements après une date donnée
 */

class EvenementController extends AbstractController
{
    /**
     * @Route("/evenement", name="evenement")
     * Création d'un évenement par un vendeur
     * Requête de type POST
     * @return id de l'évenement créé
     */
    public function createEvenement(Request $request)
    {
        // On considère que le vendeur ui crée l'évenement existe bien dans la 
        // base de données
        // Cet évenement sera créé à partir de l'interface du vendeur...
        // Une vérification est faite sur la date de l'évenement.
        // Un évenement ne peut être créé dans le passé.

        $entityManager = $this->getDoctrine()->getManager();

        // Conversion du JSON en tab associatif PHP
        $data = json_decode($request->getContent(), true);

        $now = new DateTime(); // Date du jour
        $date = new DateTime($data["date"]);

        $evenement = new Evenement();
        $evenement->setNom($data["nom"]);
        $evenement->setLieu($data["lieu"]);
        $evenement->setPrixTicket($data["prixTicket"]);
        $evenement->setDate($date);
        $evenement->setDescription(
            isset($data["description"]) ? $data["description"] : ""
        );
        $description = $evenement->getDescription();
        $evenement->setType(
            isset($data["type"]) ? $data["type"] : ""
        );
        $type = $evenement->getType();
        $evenement->setVendeur($data["vendeur"]);

        $result = array();

        // Si évenement est dans le passé
        if ($date < $now) {
            $result["message"] = "La date est dans le passé";
            $response = new JsonResponse($result, 419);
            return $response;
        }

        // On vérifie si l'évenement qu'on veut créer n'existe pas déjà

        $repository = $this->getDoctrine()->getRepository(Evenement::class);
        $evenement_test = $repository->findOneby([
            "nom" => $data["nom"],
            "type" => $type,
            "date" => $date,
            "lieu" => $data["lieu"],
            "prixTicket" => $data["prixTicket"]
        ]);

        if ($evenement_test === NULL) { // donc évenement unique
            $entityManager->persist($evenement);
            $entityManager->flush();

            // Après son insertion on retourne son ID
            $repository = $this->getDoctrine()->getRepository(Evenement::class);
            $evenement = $repository->findOneBy([
                "nom" => $data["nom"],
                "description" => $description,
                "type" => $type,
                "date" => $date,
                "lieu" => $data["lieu"],
                "prixTicket" => $data["prixTicket"],
                "vendeur" => $data["vendeur"],
            ]);

            $result["id"] = $evenement->getId();
            $response = new JsonResponse($result);
        } else {
            $result["message"] = "Cet évenement existe déjà";
            $response = new JsonResponse($result, 419);
        }


        return $response;
    }

    /**
     * @Route("/evenement/{vendeur}", name="evenement_vendeur")
     * vendeur c'est le nom public du vendeur.
     * Si la route est du type /evenement/utilisateur => c'est à dire qu'on va lister tous les événements. nécessaire pour l'interface de l'utilisateur
     * Requête de type GET
     * Ensemble d'événements pour un vendeur donné. 
     * Ne retourne pas les événements qui sont dans le passé
     * Se réfère à la date du jour pour retourner les événements
     * @return array evenements
     */
    public function getEvenements($vendeur) {
        $entityManager = $this->getDoctrine()->getManager();

        $now = new DateTime();
        $date_ref_str = date_format($now, "Y-m-d");

        $query = "SELECT * FROM evenement WHERE date >= '$date_ref_str' AND vendeur = '$vendeur'";
        if ($vendeur == "utilisateur") {
            $query = "SELECT * FROM evenement WHERE date >= '$date_ref_str'";
        }
        $statement = $entityManager->getConnection()->prepare($query);
        $statement->execute();
        $resultats = $statement->fetchAll();

        return new JsonResponse($resultats);
    }
}

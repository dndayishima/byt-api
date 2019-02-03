<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Utilisateur;

use \DateTime;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/utilisateur", name="utilisateur")
     * Création d'un utilisateur (inscription)
     * Requête de type POST
     * @return id de l'utilisateur inscrit
     */
    public function createUtilisateur(Request $request) {

        // Création d'un utilisateur en considérant que la validité des champs
        // a été vérifiée du côté du front-end

        $entityManager = $this->getDoctrine()->getManager();

        // Conversion du JSON en tableau associtif PHP
        $data = json_decode($request->getContent(), true);

        $naissance = new DateTime($data["naissance"]);

        $utilisateur = new Utilisateur();
        $utilisateur->setUsername($data["username"]);
        $utilisateur->setPassword($data["password"]);
        $utilisateur->setNom($data["nom"]);
        $utilisateur->setPrenom($data["prenom"]);
        $utilisateur->setNaissance($naissance);
        $utilisateur->setTelephone1(
            isset($data["telephone1"]) ? $data["telephone1"] : 0
        );
        $telephone1 = $utilisateur->getTelephone1();
        $utilisateur->setTelephone2(
            isset($data["telephone2"]) ? $data["telephone2"] : 0
        );
        $telephone2 = $utilisateur->getTelephone2();
        $utilisateur->setTelephone3(
            isset($data["telephone3"]) ? $data["telephone3"] : 0
        );
        $telephone3 = $utilisateur->getTelephone3();

        // Avant d'insérer un nouvel utilisateur dans la base de données
        // on vérifie que le nom d'utilisateur est unique.
        // C'est une donnée qui sera utilisée pour la connexion
        // donc il faut qu'elle soit unique.
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateur_test = $repository->findOneBy([
            "username" => $data["username"]
        ]);

        $result = array();

        if ($utilisateur_test === NULL) { // donc il est unique
            $entityManager->persist($utilisateur);

            $entityManager->flush();

            // Après insertion du nouvel utilisateur dans la
            // base de données, retourner son ID

            $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
            $utilisateur = $repository->findOneBy([
                "username" => $data["username"],
                "nom" => $data["nom"],
                "prenom" => $data["prenom"],
                "naissance" => $naissance
            ]);
            
            $result["id"] = $utilisateur->getId();
            $response = new JsonResponse($result);
        } else {
            $result["message"] = "Ce nom d'utilisateur existe déjà";
            $response = new JsonResponse($result, 419);
        }

        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/utilisateur/{id}", name="get_utilisateur")
     * Information sur un utilisateur
     * Requête de type GET
     * @return Utilisateur
     */
    public function getUtilisateur($id) {
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateur = $repository->find($id);
        $response = array();
        if ($utilisateur !== NULL) {
            
            $response["id"] = $utilisateur->getId();
            $response["username"] = $utilisateur->getUsername();
            $response["password"] = "";
            $response["nom"] = $utilisateur->getNom();
            $response["prenom"] = $utilisateur->getPrenom();

            $naissance = $utilisateur->getNaissance();
            $naissance = $naissance->format("Y-m-d");
            $response["naissance"] = $naissance;
            
            $response["telephone1"] = ($utilisateur->getTelephone1() === NULL ? 0 : $utilisateur->getTelephone1());
            $response["telephone2"] = ($utilisateur->getTelephone2() === NULL ? 0 : $utilisateur->getTelephone2());
            $response["telephone3"] = ($utilisateur->getTelephone3() === NULL ? 0 : $utilisateur->getTelephone3());

        }
        $result = new JsonResponse($response);
        $result->headers->set("Access-Control-Allow-Origin", "*");
        return $result;
    }


    /**
     * @Route("/utilisateur/{username}/{password}", name="connexion")
     * Connextion d'un utilisateur
     * Requête de type GET 
     * @return Utilisateur
     */
    public function connexion($username, $password) {
        $repository = $this->getDoctrine()->getRepository(Utilisateur::class);
        $utilisateur = $repository->findOneBy([
            "username" => $username,
            "password" => $password
        ]);
        $response = array();
        if ($utilisateur !== NULL) {
            
            $response["id"] = $utilisateur->getId();
            $response["username"] = $utilisateur->getUsername();
            $response["password"] = "";
            $response["nom"] = $utilisateur->getNom();
            $response["prenom"] = $utilisateur->getPrenom();

            $naissance = $utilisateur->getNaissance();
            $naissance = $naissance->format("Y-m-d");
            $response["naissance"] = $naissance;
            
            $response["telephone1"] = ($utilisateur->getTelephone1() === NULL ? 0 : $utilisateur->getTelephone1());
            $response["telephone2"] = ($utilisateur->getTelephone2() === NULL ? 0 : $utilisateur->getTelephone2());
            $response["telephone3"] = ($utilisateur->getTelephone3() === NULL ? 0 : $utilisateur->getTelephone3());

        }
        
        $result = new JsonResponse($response);
        $result->headers->set("Access-Control-Allow-Origin", "*");
        return $result;
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Vendeur;

class VendeurController extends AbstractController
{
    /**
     * @Route("/vendeur", name="vendeur")
     * Création d'un vendeur
     * Requête de type POST
     * @return id du vendeur créé
     */
    public function createVendeur(Request $request) {

        // Création d'un vendeur en considérant que la validité des champs
        // a été vérifiée du côté du front-end

        $entityManager = $this->getDoctrine()->getManager();

        // Conversion du JSON en tableau associtif PHP
        $data = json_decode($request->getContent(), true);

        $vendeur = new Vendeur();
        $vendeur->setNomPublic($data["nomPublic"]);
        $vendeur->setPassword($data["password"]);
        $vendeur->setNom($data["nom"]);
        $vendeur->setPrenom($data["prenom"]);
        $vendeur->setTelephone1(
            isset($data["telephone1"]) ? $data["telephone1"] : 0
        );
        $telephone1 = $vendeur->getTelephone1();
        $vendeur->setTelephone2(
            isset($data["telephone2"]) ? $data["telephone2"] : 0
        );
        $telephone2 = $vendeur->getTelephone2();
        $vendeur->setTelephone3(
            isset($data["telephone3"]) ? $data["telephone3"] : 0
        );
        $telephone3 = $vendeur->getTelephone3();

        // Avant d'insérer un nouveau vendeur dans la base de données
        // on vérifie que son nom public est unique.
        // C'est une donnée qui sera utilisée pour la connexion
        // donc il faut qu'elle soit unique.
        $repository = $this->getDoctrine()->getRepository(Vendeur::class);
        $vendeur_test = $repository->findOneBy([
            "nomPublic" => $data["nomPublic"]
        ]);

        $result = array();

        if ($vendeur_test === NULL) { // donc il est unique
            $entityManager->persist($vendeur);

            $entityManager->flush();

            // Après insertion du nouveau vendeur dans la
            // base de données, retourner son ID

            $repository = $this->getDoctrine()->getRepository(Vendeur::class);
            $vendeur = $repository->findOneBy([
                "nomPublic" => $data["nomPublic"],
                "nom" => $data["nom"],
                "prenom" => $data["prenom"]
            ]);
            
            $result["id"] = $vendeur->getId();
            $response = new JsonResponse($result);
        } else {
            $result["message"] = "Ce nom public de vendeur existe déjà";
            $response = new JsonResponse($result, 419);
        }

        $response->headers->set("Access-Control-Allow-Origin", "*");
        return $response;
    }

    /**
     * @Route("/vendeur/{id}", name="get_vendeur")
     * Information sur un vendeur
     * Requête de type GET
     * @return Vendeur
     */
    public function getVendeur($id) {
        $repository = $this->getDoctrine()->getRepository(Vendeur::class);
        $vendeur = $repository->find($id);
        $response = array();
        if ($vendeur !== NULL) {
            
            $response["id"] = $vendeur->getId();
            $response["nomPublic"] = $vendeur->getNomPublic();
            $response["password"] = "";
            $response["nom"] = $vendeur->getNom();
            $response["prenom"] = $vendeur->getPrenom();
            
            $response["telephone1"] = ($vendeur->getTelephone1() === NULL ? 0 : $vendeur->getTelephone1());
            $response["telephone2"] = ($vendeur->getTelephone2() === NULL ? 0 : $vendeur->getTelephone2());
            $response["telephone3"] = ($vendeur->getTelephone3() === NULL ? 0 : $vendeur->getTelephone3());

        }
        $result = new JsonResponse($response);
        $result->headers->set("Access-Control-Allow-Origin", "*");
        return $result;
    }

    /**
     * @Route("/vendeur/connexion/{nomPublic}/{password}", name="connexion_vendeur")
     * Connexion d'un vendeur
     * Requête de type GET 
     * @return Vendeur
     */
    public function connexion($nomPublic, $password) {
        $repository = $this->getDoctrine()->getRepository(Vendeur::class);
        $vendeur = $repository->findOneBy([
            "nomPublic" => $nomPublic,
            "password" => $password
        ]);
        $response = array();
        if ($vendeur !== NULL) {
            
            $response["id"] = $vendeur->getId();
            $response["nomPublic"] = $vendeur->getNomPublic();
            $response["password"] = "";
            $response["nom"] = $vendeur->getNom();
            $response["prenom"] = $vendeur->getPrenom();
            
            $response["telephone1"] = ($vendeur->getTelephone1() === NULL ? 0 : $vendeur->getTelephone1());
            $response["telephone2"] = ($vendeur->getTelephone2() === NULL ? 0 : $vendeur->getTelephone2());
            $response["telephone3"] = ($vendeur->getTelephone3() === NULL ? 0 : $vendeur->getTelephone3());

        }
        $result = new JsonResponse($response);
        $result->headers->set("Access-Control-Allow-Origin", "*");
        return $result;
    }

    /**
     * @Route("/vendeur/get/{nomPublic}", name="get_vendeur_by_name")
     * Information d'un vendeur à partir de son nomPublic
     * Requête de type GET
     * @return vendeur
     */
    public function getVendeurByName($nomPublic) {
        $repository = $this->getDoctrine()->getRepository(Vendeur::class);
        $vendeur = $repository->findOneby([
            "nomPublic" => $nomPublic
        ]);
        $response = array();
        if ($vendeur !== NULL) {
            $response["id"] = $vendeur->getId();
            $response["nomPublic"] = $vendeur->getNomPublic();
            $response["nom"] = $vendeur->getNom();
            $response["prenom"] = $vendeur->getPrenom();
            
            $response["telephone1"] = ($vendeur->getTelephone1() === NULL ? 0 : $vendeur->getTelephone1());
            $response["telephone2"] = ($vendeur->getTelephone2() === NULL ? 0 : $vendeur->getTelephone2());
            $response["telephone3"] = ($vendeur->getTelephone3() === NULL ? 0 : $vendeur->getTelephone3());
        }
        $result = new JsonResponse($response);
        $result->headers->set("Access-Control-Allow-Origin", "*");
        return $result;
    }
}

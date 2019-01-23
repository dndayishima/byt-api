<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\VendeurRepository")
 */
class Vendeur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nomPublic;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $telephone1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $telephone2;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $telephone3;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNomPublic(): ?string
    {
        return $this->nomPublic;
    }

    public function setNomPublic(string $nomPublic): self
    {
        $this->nomPublic = $nomPublic;

        return $this;
    }

    public function getTelephone1(): ?int
    {
        return $this->telephone1;
    }

    public function setTelephone1(?int $telephone1): self
    {
        $this->telephone1 = $telephone1;

        return $this;
    }

    public function getTelephone2(): ?int
    {
        return $this->telephone2;
    }

    public function setTelephone2(?int $telephone2): self
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    public function getTelephone3(): ?int
    {
        return $this->telephone3;
    }

    public function setTelephone3(?int $telephone3): self
    {
        $this->telephone3 = $telephone3;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}

<?php

class Offre
{
    private ?int $id = null;
    private string $titre = '';
    private string $description = '';
    private ?float $salaire = null;
    private string $dateLimite = '';
    private string $statut = 'brouillon';
    private ?int $idEntreprise = null;
    private ?int $idSecteur = null;
    private ?int $idVille = null;
    private ?int $idTypeContrat = null;
    private ?string $datePublication = null;

    public function __construct(private PDO $db)
    {
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function getDateLimite(): string
    {
        return $this->dateLimite;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getIdEntreprise(): ?int
    {
        return $this->idEntreprise;
    }

    public function getIdSecteur(): ?int
    {
        return $this->idSecteur;
    }

    public function getIdVille(): ?int
    {
        return $this->idVille;
    }

    public function getIdTypeContrat(): ?int
    {
        return $this->idTypeContrat;
    }

    public function getDatePublication(): ?string
    {
        return $this->datePublication;
    }

    // SETTERS

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setSalaire(?float $salaire): void
    {
        $this->salaire = $salaire;
    }

    public function setDateLimite(string $dateLimite): void
    {
        $this->dateLimite = $dateLimite;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function setIdEntreprise(?int $idEntreprise): void
    {
        $this->idEntreprise = $idEntreprise;
    }

    public function setIdSecteur(?int $idSecteur): void
    {
        $this->idSecteur = $idSecteur;
    }

    public function setIdVille(?int $idVille): void
    {
        $this->idVille = $idVille;
    }

    public function setIdTypeContrat(?int $idTypeContrat): void
    {
        $this->idTypeContrat = $idTypeContrat;
    }

    public function setDatePublication(?string $datePublication): void
    {
        $this->datePublication = $datePublication;
    }

    // ACCÈS AUX DONNÉES

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM offre WHERE id = :id LIMIT 1'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $offre = $stmt->fetch();

        return $offre ?: null;
    }
}
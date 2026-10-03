<?php

class Entreprise
{
    private ?int $id = null;
    private string $nom = '';
    private ?string $email = null;
    private ?string $telephone = null;
    private ?string $adresse = null;
    private ?int $idSecteur = null;
    private ?int $idVille = null;

    public function __construct(private PDO $db)
    {
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getIdSecteur(): ?int
    {
        return $this->idSecteur;
    }

    public function getIdVille(): ?int
    {
        return $this->idVille;
    }

    // SETTERS

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function setIdSecteur(?int $idSecteur): void
    {
        $this->idSecteur = $idSecteur;
    }

    public function setIdVille(?int $idVille): void
    {
        $this->idVille = $idVille;
    }

    // ACCÈS AUX DONNÉES

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM entreprise WHERE id = :id LIMIT 1'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $entreprise = $stmt->fetch();

        return $entreprise ?: null;
    }
}
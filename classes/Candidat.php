<?php

class Candidat
{
    private ?int $id = null;
    private string $nom = '';
    private string $prenom = '';
    private string $email = '';
    private string $motDePasse = '';
    private string $telephone = '';
    private ?int $idVille = null;
    private ?string $cvFichier = null;
    private string $statut = 'actif';

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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getIdVille(): ?int
    {
        return $this->idVille;
    }

    public function getCvFichier(): ?string
    {
        return $this->cvFichier;
    }

    public function getStatut(): string
    {
        return $this->statut;
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

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setMotDePasse(string $motDePasse): void
    {
        $this->motDePasse = $motDePasse;
    }

    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setIdVille(?int $idVille): void
    {
        $this->idVille = $idVille;
    }

    public function setCvFichier(?string $cvFichier): void
    {
        $this->cvFichier = $cvFichier;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    // ACCÈS AUX DONNÉES

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM candidat WHERE email = :email LIMIT 1'
        );

        $stmt->execute([
            'email' => $email
        ]);

        $candidat = $stmt->fetch();

        return $candidat ?: null;
    }
}
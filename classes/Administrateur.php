<?php

class Administrateur
{
    private ?int $id = null;
    private string $nom = '';
    private string $prenom = '';
    private string $email = '';
    private string $motDePasse = '';

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

    // ACCÈS AUX DONNÉES

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM administrateur WHERE email = :email LIMIT 1'
        );

        $stmt->execute([
            'email' => $email
        ]);

        $administrateur = $stmt->fetch();

        return $administrateur ?: null;
    }
}
<?php

class Candidature
{
    private ?int $id = null;
    private ?int $idCandidat = null;
    private ?int $idOffre = null;
    private string $lettreMotivation = '';
    private string $statut = 'en_attente';
    private ?string $dateCandidature = null;

    public function __construct(private PDO $db)
    {
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCandidat(): ?int
    {
        return $this->idCandidat;
    }

    public function getIdOffre(): ?int
    {
        return $this->idOffre;
    }

    public function getLettreMotivation(): string
    {
        return $this->lettreMotivation;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getDateCandidature(): ?string
    {
        return $this->dateCandidature;
    }

    // SETTERS

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setIdCandidat(?int $idCandidat): void
    {
        $this->idCandidat = $idCandidat;
    }

    public function setIdOffre(?int $idOffre): void
    {
        $this->idOffre = $idOffre;
    }

    public function setLettreMotivation(string $lettreMotivation): void
    {
        $this->lettreMotivation = $lettreMotivation;
    }

    public function setStatut(string $statut): void
    {
        $this->statut = $statut;
    }

    public function setDateCandidature(?string $dateCandidature): void
    {
        $this->dateCandidature = $dateCandidature;
    }

    // ACCÈS AUX DONNÉES

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM candidature WHERE id = :id LIMIT 1'
        );

        $stmt->execute([
            'id' => $id
        ]);

        $candidature = $stmt->fetch();

        return $candidature ?: null;
    }

    public function existsForCandidateAndOffer(
        int $idCandidat,
        int $idOffre
    ): bool {
        $stmt = $this->db->prepare(
            'SELECT id
             FROM candidature
             WHERE id_candidat = :id_candidat
             AND id_offre = :id_offre
             LIMIT 1'
        );

        $stmt->execute([
            'id_candidat' => $idCandidat,
            'id_offre' => $idOffre
        ]);

        return (bool) $stmt->fetchColumn();
    }
}
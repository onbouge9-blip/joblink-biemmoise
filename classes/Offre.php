<?php

class Offre
{
    public function __construct(private PDO $db)
    {
    }

    public function getPubliees(): array
    {
        $sql = "SELECT
                    o.id,
                    o.titre,
                    o.description,
                    o.salaire,
                    o.date_limite,
                    o.date_publication,
                    e.nom AS entreprise,
                    s.libelle AS secteur,
                    v.libelle AS ville,
                    tc.libelle AS type_contrat
                FROM offre o
                INNER JOIN entreprise e ON o.id_entreprise = e.id
                INNER JOIN secteur s ON o.id_secteur = s.id
                INNER JOIN ville v ON o.id_ville = v.id
                INNER JOIN type_contrat tc ON o.id_type_contrat = tc.id
                WHERE o.statut = 'publiee'
                ORDER BY o.date_publication DESC";

        return $this->db->query($sql)->fetchAll();
    }
}

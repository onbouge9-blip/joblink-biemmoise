<?php

class Candidat
{
    public function __construct(private PDO $db)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM candidat WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        $candidat = $stmt->fetch();
        return $candidat ?: null;
    }
}

<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    exit('Candidat invalide.');
}

/* Récupération du candidat */
$stmt = $pdo->prepare("
    SELECT
        c.id,
        c.nom,
        c.prenom,
        c.email,
        c.telephone,
        c.cv_fichier,
        c.statut,
        c.date_inscription,
        v.libelle AS ville
    FROM candidat c
    LEFT JOIN ville v ON c.id_ville = v.id
    WHERE c.id = :id
");

$stmt->execute(['id' => $id]);

$candidat = $stmt->fetch();

if (!$candidat) {
    exit('Candidat introuvable.');
}

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil candidat</title>
</head>

<body>

<h1>Profil du candidat</h1>

<p>
    <strong>Nom :</strong>
    <?= htmlspecialchars($candidat['nom']) ?>
</p>

<p>
    <strong>Prénom :</strong>
    <?= htmlspecialchars($candidat['prenom']) ?>
</p>

<p>
    <strong>Email :</strong>
    <?= htmlspecialchars($candidat['email']) ?>
</p>

<p>
    <strong>Téléphone :</strong>
    <?= htmlspecialchars($candidat['telephone']) ?>
</p>

<p>
    <strong>Ville :</strong>
    <?= htmlspecialchars($candidat['ville'] ?? '') ?>
</p>

<p>
    <strong>Statut :</strong>
    <?= htmlspecialchars($candidat['statut']) ?>
</p>

<p>
    <strong>Date d'inscription :</strong>
    <?= htmlspecialchars($candidat['date_inscription']) ?>
</p>

<p>
    <strong>CV :</strong>

    <?php if (!empty($candidat['cv_fichier'])): ?>

        <a
            href="../../uploads/cv/<?= htmlspecialchars($candidat['cv_fichier']) ?>"
            target="_blank"
        >
            📄 Voir le CV
        </a>

    <?php else: ?>

        Aucun CV envoyé.

    <?php endif; ?>

</p>

<hr>

<a href="index.php">← Retour à la liste des candidats</a>

</body>
</html>
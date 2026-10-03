<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

/*
|--------------------------------------------------------------------------
| Récupération de l'identifiant de la candidature
|--------------------------------------------------------------------------
*/

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die('Candidature invalide.');
}

/*
|--------------------------------------------------------------------------
| Récupération des informations de la candidature
|--------------------------------------------------------------------------
*/

$q = $pdo->prepare("
    SELECT
        c.id,
        c.lettre_motivation,
        c.statut,
        c.date_candidature,

        ca.id AS id_candidat,
        ca.nom AS candidat_nom,
        ca.prenom AS candidat_prenom,
        ca.email AS candidat_email,
        ca.telephone AS candidat_telephone,
        ca.cv_fichier,
        ca.statut AS candidat_statut,

        v.libelle AS ville_candidat,

        o.id AS id_offre,
        o.titre AS offre_titre,
        o.description AS offre_description,
        o.salaire,
        o.date_limite,
        o.statut AS offre_statut,

        e.id AS id_entreprise,
        e.nom AS entreprise_nom,
        e.email AS entreprise_email,
        e.telephone AS entreprise_telephone,

        vo.libelle AS ville_offre,

        s.libelle AS secteur,
        tc.libelle AS type_contrat

    FROM candidature c

    JOIN candidat ca
        ON c.id_candidat = ca.id

    LEFT JOIN ville v
        ON ca.id_ville = v.id

    JOIN offre o
        ON c.id_offre = o.id

    JOIN entreprise e
        ON o.id_entreprise = e.id

    LEFT JOIN ville vo
        ON o.id_ville = vo.id

    LEFT JOIN secteur s
        ON o.id_secteur = s.id

    LEFT JOIN type_contrat tc
        ON o.id_type_contrat = tc.id

    WHERE c.id = :id

    LIMIT 1
");

$q->execute([
    'id' => $id
]);

$candidature = $q->fetch();

if (!$candidature) {
    die('Candidature introuvable.');
}

?>

<!doctype html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Détail de la candidature</title>

</head>

<body>

<h1>Détail de la candidature</h1>

<hr>

<!-- =========================================================
     INFORMATIONS SUR LE CANDIDAT
========================================================= -->

<h2>👤 Informations du candidat</h2>

<p>
    <strong>Nom :</strong>
    <?= htmlspecialchars($candidature['candidat_nom']) ?>
</p>

<p>
    <strong>Prénom :</strong>
    <?= htmlspecialchars($candidature['candidat_prenom']) ?>
</p>

<p>
    <strong>Email :</strong>
    <?= htmlspecialchars($candidature['candidat_email']) ?>
</p>

<p>
    <strong>Téléphone :</strong>
    <?= htmlspecialchars($candidature['candidat_telephone']) ?>
</p>

<p>
    <strong>Ville :</strong>
    <?= htmlspecialchars($candidature['ville_candidat'] ?? '') ?>
</p>

<p>
    <strong>Statut du candidat :</strong>
    <?= htmlspecialchars($candidature['candidat_statut']) ?>
</p>

<?php if (!empty($candidature['cv_fichier'])): ?>

    <p>
        <strong>CV :</strong>

        <a
            href="../../uploads/cv/<?= htmlspecialchars($candidature['cv_fichier']) ?>"
            target="_blank"
        >
            📄 Voir / télécharger le CV
        </a>
    </p>

<?php else: ?>

    <p>
        <strong>CV :</strong>
        Aucun CV disponible.
    </p>

<?php endif; ?>


<hr>


<!-- =========================================================
     INFORMATIONS SUR L'OFFRE
========================================================= -->

<h2>💼 Offre concernée</h2>

<p>
    <strong>Titre :</strong>
    <?= htmlspecialchars($candidature['offre_titre']) ?>
</p>

<p>
    <strong>Entreprise :</strong>
    <?= htmlspecialchars($candidature['entreprise_nom']) ?>
</p>

<p>
    <strong>Secteur :</strong>
    <?= htmlspecialchars($candidature['secteur'] ?? '') ?>
</p>

<p>
    <strong>Ville :</strong>
    <?= htmlspecialchars($candidature['ville_offre'] ?? '') ?>
</p>

<p>
    <strong>Type de contrat :</strong>
    <?= htmlspecialchars($candidature['type_contrat'] ?? '') ?>
</p>

<p>
    <strong>Salaire :</strong>

    <?php if ($candidature['salaire'] !== null): ?>

        <?= htmlspecialchars($candidature['salaire']) ?> FCFA

    <?php else: ?>

        Non précisé

    <?php endif; ?>

</p>

<p>
    <strong>Date limite :</strong>
    <?= htmlspecialchars($candidature['date_limite']) ?>
</p>

<p>
    <strong>Statut de l'offre :</strong>
    <?= htmlspecialchars($candidature['offre_statut']) ?>
</p>

<p>
    <strong>Description :</strong>
</p>

<p>
    <?= nl2br(htmlspecialchars($candidature['offre_description'])) ?>
</p>


<hr>


<!-- =========================================================
     INFORMATIONS SUR L'ENTREPRISE
========================================================= -->

<h2>🏢 Entreprise</h2>

<p>
    <strong>Nom :</strong>
    <?= htmlspecialchars($candidature['entreprise_nom']) ?>
</p>

<p>
    <strong>Email :</strong>
    <?= htmlspecialchars($candidature['entreprise_email'] ?? '') ?>
</p>

<p>
    <strong>Téléphone :</strong>
    <?= htmlspecialchars($candidature['entreprise_telephone'] ?? '') ?>
</p>


<hr>


<!-- =========================================================
     INFORMATIONS SUR LA CANDIDATURE
========================================================= -->

<h2>📨 Candidature</h2>

<p>
    <strong>Date de candidature :</strong>
    <?= htmlspecialchars($candidature['date_candidature']) ?>
</p>

<p>
    <strong>Statut :</strong>
    <?= htmlspecialchars($candidature['statut']) ?>
</p>

<p>
    <strong>Lettre de motivation :</strong>
</p>

<div
    style="
        border:1px solid #ccc;
        padding:15px;
        max-width:800px;
        white-space:normal;
    "
>
    <?= nl2br(htmlspecialchars($candidature['lettre_motivation'])) ?>
</div>


<hr>


<!-- =========================================================
     NAVIGATION
========================================================= -->

<p>

    <a href="index.php">
        ← Retour aux candidatures
    </a>

    &nbsp; | &nbsp;

    <a href="../dashboard.php">
        Retour au dashboard
    </a>

</p>

</body>

</html>
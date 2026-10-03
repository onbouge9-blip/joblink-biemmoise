<?php

require_once __DIR__ . '/../classes/Database.php';

$pdo = (new Database())->getConnection();

/*
|--------------------------------------------------------------------------
| Récupération des filtres
|--------------------------------------------------------------------------
*/

$mot = trim($_GET['mot'] ?? '');
$sec = (int)($_GET['secteur'] ?? 0);
$ville = (int)($_GET['ville'] ?? 0);
$type = (int)($_GET['type_contrat'] ?? 0);

/*
|--------------------------------------------------------------------------
| Requête principale
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        o.id,
        o.titre,
        o.description,
        e.nom AS entreprise,
        s.libelle AS secteur,
        v.libelle AS ville,
        tc.libelle AS type_contrat
    FROM offre o

    INNER JOIN entreprise e
        ON o.id_entreprise = e.id

    INNER JOIN secteur s
        ON o.id_secteur = s.id

    INNER JOIN ville v
        ON o.id_ville = v.id

    INNER JOIN type_contrat tc
        ON o.id_type_contrat = tc.id

    WHERE o.statut = 'publiee'
";

$params = [];

/*
|--------------------------------------------------------------------------
| Filtre mot-clé
|--------------------------------------------------------------------------
*/

if ($mot !== '') {

    $sql .= "
        AND (
            o.titre LIKE :o_mot
            OR o.description LIKE :d_mot
            OR e.nom LIKE :e_mot
        )
    ";

    $motRecherche = '%' . $mot . '%';

    $params['o_mot'] = $motRecherche;
    $params['d_mot'] = $motRecherche;
    $params['e_mot'] = $motRecherche;
}

/*
|--------------------------------------------------------------------------
| Filtre secteur
|--------------------------------------------------------------------------
*/

if ($sec > 0) {

    $sql .= " AND o.id_secteur = :secteur";

    $params['secteur'] = $sec;
}

/*
|--------------------------------------------------------------------------
| Filtre ville
|--------------------------------------------------------------------------
*/

if ($ville > 0) {

    $sql .= " AND o.id_ville = :ville";

    $params['ville'] = $ville;
}

/*
|--------------------------------------------------------------------------
| Filtre type de contrat
|--------------------------------------------------------------------------
*/

if ($type > 0) {

    $sql .= " AND o.id_type_contrat = :type_contrat";

    $params['type_contrat'] = $type;
}

/*
|--------------------------------------------------------------------------
| Tri
|--------------------------------------------------------------------------
*/

$sql .= " ORDER BY o.date_publication DESC";

/*
|--------------------------------------------------------------------------
| Exécution
|--------------------------------------------------------------------------
*/

$q = $pdo->prepare($sql);
$q->execute($params);

$offres = $q->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Données nécessaires aux filtres
|--------------------------------------------------------------------------
*/
$stmtSecteurs = $pdo->prepare(
    "SELECT id, libelle
     FROM secteur
     ORDER BY libelle"
);
$stmtSecteurs->execute();
$secteurs = $stmtSecteurs->fetchAll(PDO::FETCH_ASSOC);

$stmtVilles = $pdo->prepare(
    "SELECT id, libelle
     FROM ville
     ORDER BY libelle"
);
$stmtVilles->execute();
$villes = $stmtVilles->fetchAll(PDO::FETCH_ASSOC);

$stmtTypes = $pdo->prepare(
    "SELECT id, libelle
     FROM type_contrat
     ORDER BY libelle"
);
$stmtTypes->execute();
$types = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Offres d'emploi - JobLink Bénin</title>

</head>

<body>

    <h1>Offres d'emploi</h1>

    <!--
    ============================================================
    FORMULAIRE DE RECHERCHE
    ============================================================
    -->

    <form method="get">

        <!-- Mot-clé -->

        <input
            type="text"
            name="mot"
            value="<?= htmlspecialchars($mot) ?>"
            placeholder="Mot-clé"
        >

        <!-- Secteur -->

        <select name="secteur">

            <option value="0">
                Tous les secteurs
            </option>

            <?php foreach ($secteurs as $x): ?>

                <option
                  value="<?= (int)$x['id'] ?>"
                    <?= $sec == $x['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($x['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <!-- Ville -->

        <select name="ville">

            <option value="0">
                Toutes les villes
            </option>

            <?php foreach ($villes as $x): ?>

                <option
                    value="<?= (int)$x['id'] ?>"
                    <?= $ville == $x['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($x['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <!-- Type de contrat -->

        <select name="type_contrat">

            <option value="0">
                Tous les contrats
            </option>

            <?php foreach ($types as $x): ?>

                <option
                    value="<?= (int)$x['id'] ?>"
                    <?= $type == $x['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($x['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <button type="submit">
            Rechercher
        </button>

    </form>

    <hr>

    <!--
    ============================================================
    RÉSULTATS
    ============================================================
    -->

    <?php if (empty($offres)): ?>

        <p>
            Aucune offre ne correspond à votre recherche.
        </p>

    <?php else: ?>

        <p>
            <?= count($offres) ?>
            offre(s) trouvée(s).
        </p>

        <?php foreach ($offres as $o): ?>

            <article>

                <h2>
                    <?= htmlspecialchars($o['titre']) ?>
                </h2>

                <p>
                    <strong>Entreprise :</strong>
                    <?= htmlspecialchars($o['entreprise']) ?>
                </p>

                <p>
                    <strong>Secteur :</strong>
                    <?= htmlspecialchars($o['secteur']) ?>
                </p>

                <p>
                    <strong>Ville :</strong>
                    <?= htmlspecialchars($o['ville']) ?>
                </p>

                <p>
                    <strong>Type de contrat :</strong>
                    <?= htmlspecialchars($o['type_contrat']) ?>
                </p>

                <p>
                    <?= htmlspecialchars(
                        mb_substr($o['description'], 0, 200)
                    ) ?>
                    ...
                </p>

                <a href="offre.php?id=<?= (int)$o['id'] ?>">
                    Voir l'offre
                </a>

            </article>

            <hr>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a href="dashboard.php">
            Retour au tableau de bord
        </a>
    </p>

</body>

</html>
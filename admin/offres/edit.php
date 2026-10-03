<?php

require_once __DIR__.'/../../classes/Database.php';
require_once __DIR__.'/../../classes/Session.php';
require_once __DIR__.'/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('Offre invalide.');
}

/* Récupération de l'offre */
$q = $pdo->prepare("
    SELECT *
    FROM offre
    WHERE id = :id
");

$q->execute(['id' => $id]);

$offre = $q->fetch();

if (!$offre) {
    exit('Offre introuvable.');
}

/* Données nécessaires aux listes */
$stmtEntreprises = $pdo->prepare(
    "SELECT id, nom FROM entreprise ORDER BY nom"
);
$stmtEntreprises->execute();
$entreprises = $stmtEntreprises->fetchAll();

$stmtSecteurs = $pdo->prepare(
    "SELECT id, libelle FROM secteur ORDER BY libelle"
);
$stmtSecteurs->execute();
$secteurs = $stmtSecteurs->fetchAll();

$stmtVilles = $pdo->prepare(
    "SELECT id, libelle FROM ville ORDER BY libelle"
);
$stmtVilles->execute();
$villes = $stmtVilles->fetchAll();

$stmtTypesContrat = $pdo->prepare(
    "SELECT id, libelle FROM type_contrat ORDER BY libelle"
);
$stmtTypesContrat->execute();
$typesContrat = $stmtTypesContrat->fetchAll();

$msg = '';
$err = '';

/* Traitement du formulaire */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $salaire = $_POST['salaire'] ?? '';
    $date_limite = $_POST['date_limite'] ?? '';
    $statut = $_POST['statut'] ?? '';

    $id_entreprise = (int)($_POST['id_entreprise'] ?? 0);
    $id_secteur = (int)($_POST['id_secteur'] ?? 0);
    $id_ville = (int)($_POST['id_ville'] ?? 0);
    $id_type_contrat = (int)($_POST['id_type_contrat'] ?? 0);

    if ($titre === '') {
        $err = 'Le titre est obligatoire.';
    } elseif ($description === '') {
        $err = 'La description est obligatoire.';
    } elseif ($date_limite === '') {
        $err = 'La date limite est obligatoire.';
    } elseif (!in_array($statut, ['brouillon', 'publiee', 'cloturee'], true)) {
        $err = 'Statut invalide.';
    } elseif ($id_entreprise <= 0 || $id_secteur <= 0 || $id_ville <= 0 || $id_type_contrat <= 0) {
        $err = 'Veuillez sélectionner toutes les informations nécessaires.';
    } else {

        /*
         * Si l'offre devient publiée et qu'elle n'avait
         * pas encore de date de publication, on la crée.
         */
        $date_publication = $offre['date_publication'];

        if ($statut === 'publiee' && empty($date_publication)) {
            $date_publication = date('Y-m-d H:i:s');
        }

        /*
         * Si l'offre n'est pas publiée,
         * on ne crée pas de date de publication.
         */
        if ($statut !== 'publiee') {
            $date_publication = null;
        }

        $q = $pdo->prepare("
            UPDATE offre
            SET
                titre = :titre,
                description = :description,
                salaire = :salaire,
                date_limite = :date_limite,
                statut = :statut,
                id_entreprise = :id_entreprise,
                id_secteur = :id_secteur,
                id_ville = :id_ville,
                id_type_contrat = :id_type_contrat,
                date_publication = :date_publication
            WHERE id = :id
        ");

        $q->execute([
            'titre' => $titre,
            'description' => $description,
            'salaire' => $salaire !== '' ? $salaire : null,
            'date_limite' => $date_limite,
            'statut' => $statut,
            'id_entreprise' => $id_entreprise,
            'id_secteur' => $id_secteur,
            'id_ville' => $id_ville,
            'id_type_contrat' => $id_type_contrat,
            'date_publication' => $date_publication,
            'id' => $id
        ]);

        $msg = 'Offre modifiée avec succès.';

        /* Rechargement de l'offre */
        $q = $pdo->prepare("
            SELECT *
            FROM offre
            WHERE id = :id
        ");

        $q->execute(['id' => $id]);

        $offre = $q->fetch();
    }
}

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier une offre</title>
</head>

<body>

<h1>Modifier l'offre</h1>

<?php if ($msg): ?>
    <p><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<?php if ($err): ?>
    <p><?= htmlspecialchars($err) ?></p>
<?php endif; ?>

<form method="post">
   <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
    <p>
        <label>Titre</label><br>
        <input
            type="text"
            name="titre"
            value="<?= htmlspecialchars($offre['titre']) ?>"
            required
        >
    </p>

    <p>
        <label>Description</label><br>
        <textarea
            name="description"
            rows="8"
            required
        ><?= htmlspecialchars($offre['description']) ?></textarea>
    </p>

    <p>
        <label>Salaire</label><br>
        <input
            type="number"
            step="0.01"
            name="salaire"
            value="<?= htmlspecialchars($offre['salaire'] ?? '') ?>"
        >
    </p>

    <p>
        <label>Date limite</label><br>
        <input
            type="date"
            name="date_limite"
            value="<?= htmlspecialchars($offre['date_limite']) ?>"
            required
        >
    </p>

    <p>
        <label>Statut</label><br>

        <select name="statut" required>

            <option
                value="brouillon"
                <?= $offre['statut'] === 'brouillon' ? 'selected' : '' ?>
            >
                Brouillon
            </option>

            <option
                value="publiee"
                <?= $offre['statut'] === 'publiee' ? 'selected' : '' ?>
            >
                Publiée
            </option>

            <option
                value="cloturee"
                <?= $offre['statut'] === 'cloturee' ? 'selected' : '' ?>
            >
                Clôturée
            </option>

        </select>
    </p>

    <p>
        <label>Entreprise</label><br>

        <select name="id_entreprise" required>

            <?php foreach ($entreprises as $e): ?>

                <option
                    value="<?= (int)$e['id'] ?>"
                    <?= (int)$offre['id_entreprise'] === (int)$e['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($e['nom']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </p>

    <p>
        <label>Secteur</label><br>

        <select name="id_secteur" required>

            <?php foreach ($secteurs as $s): ?>

                <option
                    value="<?= (int)$s['id'] ?>"
                    <?= (int)$offre['id_secteur'] === (int)$s['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($s['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </p>

    <p>
        <label>Ville</label><br>

        <select name="id_ville" required>

            <?php foreach ($villes as $v): ?>

                <option
                    value="<?= (int)$v['id'] ?>"
                    <?= (int)$offre['id_ville'] === (int)$v['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($v['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </p>

    <p>
        <label>Type de contrat</label><br>

        <select name="id_type_contrat" required>

            <?php foreach ($types as $t): ?>

                <option
                    value="<?= (int)$t['id'] ?>"
                    <?= (int)$offre['id_type_contrat'] === (int)$t['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($t['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </p>

    <button type="submit">
        Enregistrer les modifications
    </button>

</form>

<p>
    <a href="index.php">← Retour aux offres</a>
</p>

</body>

</html>
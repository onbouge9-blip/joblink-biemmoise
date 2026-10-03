<?php

require_once __DIR__.'/../../classes/Database.php';
require_once __DIR__.'/../../classes/Session.php';
require_once __DIR__.'/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    exit('Entreprise invalide.');
}

/* Récupération de l'entreprise */
$q = $pdo->prepare("
    SELECT *
    FROM entreprise
    WHERE id = :id
");

$q->execute(['id' => $id]);

$entreprise = $q->fetch();

if (!$entreprise) {
    exit('Entreprise introuvable.');
}

/* Récupération des secteurs et villes */
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

$msg = '';
$err = '';

/* Traitement du formulaire */
if ($_SERVER['REQUEST_METHOD'] === 'POST') { if (!Session::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Requête invalide.');
}

    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $id_secteur = (int)($_POST['id_secteur'] ?? 0);
    $id_ville = (int)($_POST['id_ville'] ?? 0);

    if ($nom === '') {
        $err = 'Le nom de l’entreprise est obligatoire.';
    } elseif ($id_secteur <= 0) {
        $err = 'Veuillez sélectionner un secteur.';
    } elseif ($id_ville <= 0) {
        $err = 'Veuillez sélectionner une ville.';
    } else {

        $q = $pdo->prepare("
            UPDATE entreprise
            SET
                nom = :nom,
                email = :email,
                telephone = :telephone,
                adresse = :adresse,
                id_secteur = :id_secteur,
                id_ville = :id_ville
            WHERE id = :id
        ");

        $q->execute([
            'nom' => $nom,
            'email' => $email !== '' ? $email : null,
            'telephone' => $telephone !== '' ? $telephone : null,
            'adresse' => $adresse !== '' ? $adresse : null,
            'id_secteur' => $id_secteur,
            'id_ville' => $id_ville,
            'id' => $id
        ]);

        $msg = 'Entreprise modifiée avec succès.';

        /* On recharge les données après modification */
        $q = $pdo->prepare("
            SELECT *
            FROM entreprise
            WHERE id = :id
        ");

        $q->execute(['id' => $id]);

        $entreprise = $q->fetch();
    }
}

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier une entreprise</title>
</head>

<body>

<h1>Modifier l'entreprise</h1>

<?php if ($msg): ?>
    <p><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<?php if ($err): ?>
    <p><?= htmlspecialchars($err) ?></p>
<?php endif; ?>

<form method="post">
   <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

    <p>
        <label>Nom de l'entreprise</label><br>
        <input
            type="text"
            name="nom"
            value="<?= htmlspecialchars($entreprise['nom']) ?>"
            required
        >
    </p>

    <p>
        <label>Email</label><br>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($entreprise['email'] ?? '') ?>"
        >
    </p>

    <p>
        <label>Téléphone</label><br>
        <input
            type="text"
            name="telephone"
            value="<?= htmlspecialchars($entreprise['telephone'] ?? '') ?>"
        >
    </p>

    <p>
        <label>Adresse</label><br>
        <input
            type="text"
            name="adresse"
            value="<?= htmlspecialchars($entreprise['adresse'] ?? '') ?>"
        >
    </p>

    <p>
        <label>Secteur</label><br>

        <select name="id_secteur" required>

            <option value="">-- Choisir un secteur --</option>

            <?php foreach ($secteurs as $s): ?>

                <option
                    value="<?= (int)$s['id'] ?>"
                    <?= ((int)$entreprise['id_secteur'] === (int)$s['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($s['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </p>

    <p>
        <label>Ville</label><br>

        <select name="id_ville" required>

            <option value="">-- Choisir une ville --</option>

            <?php foreach ($villes as $v): ?>

                <option
                    value="<?= (int)$v['id'] ?>"
                    <?= ((int)$entreprise['id_ville'] === (int)$v['id']) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($v['libelle']) ?>
                </option>

            <?php endforeach; ?>

        </select>
    </p>

    <button type="submit">Enregistrer les modifications</button>

</form>

<p>
    <a href="index.php">← Retour aux entreprises</a>
</p>

</body>

</html>
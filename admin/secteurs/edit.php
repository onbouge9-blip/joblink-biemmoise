<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Récupérer le secteur
$stmt = $pdo->prepare("
    SELECT id, libelle
    FROM secteur
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$secteur = $stmt->fetch();

if (!$secteur) {
    header('Location: index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { if (!Session::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Requête invalide.');
}

    $libelle = trim($_POST['libelle'] ?? '');

    if ($libelle === '') {

        $erreur = 'Le libellé du secteur est obligatoire.';

    } else {

        // Vérifier qu'un autre secteur n'utilise pas déjà ce libellé
        $stmt = $pdo->prepare("
            SELECT id
            FROM secteur
            WHERE libelle = :libelle
            AND id <> :id
            LIMIT 1
        ");

        $stmt->execute([
            'libelle' => $libelle,
            'id' => $id
        ]);

        if ($stmt->fetch()) {

            $erreur = 'Ce secteur existe déjà.';

        } else {

            // Modifier le secteur
            $stmt = $pdo->prepare("
                UPDATE secteur
                SET libelle = :libelle
                WHERE id = :id
            ");

            $stmt->execute([
                'libelle' => $libelle,
                'id' => $id
            ]);

            header('Location: index.php');
            exit;
        }
    }
}

?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier un secteur - JobLink Bénin</title>
</head>

<body>

<h1>Modifier un secteur</h1>

<?php if ($erreur !== ''): ?>

    <p style="color:red;">
        <?= htmlspecialchars($erreur) ?>
    </p>

<?php endif; ?>

<form method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
    <label for="libelle">
        Libellé du secteur :
    </label>

    <br>

    <input
        type="text"
        id="libelle"
        name="libelle"
        maxlength="100"
        required
        value="<?= htmlspecialchars($_POST['libelle'] ?? $secteur['libelle']) ?>"
    >

    <br><br>

    <button type="submit">
        Enregistrer les modifications
    </button>

</form>

<br>

<a href="index.php">
    ← Retour aux secteurs
</a>

</body>

</html>
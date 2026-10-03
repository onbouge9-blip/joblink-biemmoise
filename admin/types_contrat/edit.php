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

// Récupérer le type de contrat
$stmt = $pdo->prepare("
    SELECT id, libelle
    FROM type_contrat
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$type = $stmt->fetch();

if (!$type) {
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

        $erreur = 'Le libellé du type de contrat est obligatoire.';

    } else {

        // Vérifier qu'un autre type de contrat n'utilise pas déjà ce libellé
        $stmt = $pdo->prepare("
            SELECT id
            FROM type_contrat
            WHERE libelle = :libelle
            AND id <> :id
            LIMIT 1
        ");

        $stmt->execute([
            'libelle' => $libelle,
            'id' => $id
        ]);

        if ($stmt->fetch()) {

            $erreur = 'Ce type de contrat existe déjà.';

        } else {

            // Modifier le type de contrat
            $stmt = $pdo->prepare("
                UPDATE type_contrat
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
    <title>Modifier un type de contrat - JobLink Bénin</title>
</head>

<body>

<h1>Modifier un type de contrat</h1>

<?php if ($erreur !== ''): ?>

    <p style="color:red;">
        <?= htmlspecialchars($erreur) ?>
    </p>

<?php endif; ?>

<form method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
    <label for="libelle">
        Libellé du type de contrat :
    </label>

    <br>

    <input
        type="text"
        id="libelle"
        name="libelle"
        maxlength="100"
        required
        value="<?= htmlspecialchars($_POST['libelle'] ?? $type['libelle']) ?>"
    >

    <br><br>

    <button type="submit">
        Enregistrer les modifications
    </button>

</form>

<br>

<a href="index.php">
    ← Retour aux types de contrat
</a>

</body>

</html>
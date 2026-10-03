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

// Récupérer la ville
$stmt = $pdo->prepare("
    SELECT id, libelle
    FROM ville
    WHERE id = :id
");

$stmt->execute([
    'id' => $id
]);

$ville = $stmt->fetch();

if (!$ville) {
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

        $erreur = 'Le libellé de la ville est obligatoire.';

    } else {

        // Vérifier qu'une autre ville n'utilise pas déjà ce libellé
        $stmt = $pdo->prepare("
            SELECT id
            FROM ville
            WHERE libelle = :libelle
            AND id <> :id
            LIMIT 1
        ");

        $stmt->execute([
            'libelle' => $libelle,
            'id' => $id
        ]);

        if ($stmt->fetch()) {

            $erreur = 'Cette ville existe déjà.';

        } else {

            // Modifier la ville
            $stmt = $pdo->prepare("
                UPDATE ville
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
    <title>Modifier une ville - JobLink Bénin</title>
</head>

<body>

<h1>Modifier une ville</h1>

<?php if ($erreur !== ''): ?>

    <p style="color:red;">
        <?= htmlspecialchars($erreur) ?>
    </p>

<?php endif; ?>

<form method="post">
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
    <label for="libelle">
        Libellé de la ville :
    </label>

    <br>

    <input
        type="text"
        id="libelle"
        name="libelle"
        maxlength="100"
        required
        value="<?= htmlspecialchars($_POST['libelle'] ?? $ville['libelle']) ?>"
    >

    <br><br>

    <button type="submit">
        Enregistrer les modifications
    </button>

</form>

<br>

<a href="index.php">
    ← Retour aux villes
</a>

</body>

</html>
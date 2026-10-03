<?php

require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/Session.php';
require_once __DIR__ . '/../../classes/AdminAuth.php';

Session::start();
AdminAuth::requireLogin();

$pdo = (new Database())->getConnection();

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') { if (!Session::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Requête invalide.');
}

    $libelle = trim($_POST['libelle'] ?? '');

    if ($libelle === '') {
        $erreur = 'Le libellé du secteur est obligatoire.';
    } else {

        // Vérifier si le secteur existe déjà
        $stmt = $pdo->prepare("
            SELECT id
            FROM secteur
            WHERE libelle = :libelle
            LIMIT 1
        ");

        $stmt->execute([
            'libelle' => $libelle
        ]);

        if ($stmt->fetch()) {

            $erreur = 'Ce secteur existe déjà.';

        } else {

            // Ajouter le secteur
            $stmt = $pdo->prepare("
                INSERT INTO secteur (libelle)
                VALUES (:libelle)
            ");

            $stmt->execute([
                'libelle' => $libelle
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
    <title>Ajouter un secteur - JobLink Bénin</title>
</head>

<body>

<h1>Ajouter un secteur</h1>

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
        value="<?= htmlspecialchars($_POST['libelle'] ?? '') ?>"
    >

    <br><br>

    <button type="submit">
        Ajouter
    </button>

</form>

<br>

<a href="index.php">
    ← Retour aux secteurs
</a>

</body>

</html>
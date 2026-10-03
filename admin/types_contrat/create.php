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

        $erreur = 'Le libellé du type de contrat est obligatoire.';

    } else {

        // Vérifier si le type de contrat existe déjà
        $stmt = $pdo->prepare("
            SELECT id
            FROM type_contrat
            WHERE libelle = :libelle
            LIMIT 1
        ");

        $stmt->execute([
            'libelle' => $libelle
        ]);

        if ($stmt->fetch()) {

            $erreur = 'Ce type de contrat existe déjà.';

        } else {

            // Ajouter le type de contrat
            $stmt = $pdo->prepare("
                INSERT INTO type_contrat (libelle)
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
    <title>Ajouter un type de contrat - JobLink Bénin</title>
</head>

<body>

<h1>Ajouter un type de contrat</h1>

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
        value="<?= htmlspecialchars($_POST['libelle'] ?? '') ?>"
    >

    <br><br>

    <button type="submit">
        Ajouter
    </button>

</form>

<br>

<a href="index.php">
    ← Retour aux types de contrat
</a>

</body>

</html>
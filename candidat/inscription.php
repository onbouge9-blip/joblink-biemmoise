<?php
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Auth.php';

$msg = '';
$err = '';

try {
    $pdo = (new Database())->getConnection();
   $stmtVilles = $pdo->prepare(
    'SELECT id, libelle FROM ville ORDER BY libelle'
);
$stmtVilles->execute();
$villes = $stmtVilles->fetchAll();
} catch (PDOException $e) {
    $villes = [];
    $err = 'Impossible de charger les villes.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { if (!Session::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Requête invalide.');
}
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $tel = trim($_POST['telephone'] ?? '');
    $idVille = (int) ($_POST['id_ville'] ?? 0);
    $pass = $_POST['mot_de_passe'] ?? '';

    if (!$nom || !$prenom || !$email || !$tel || !$idVille || !$pass) {
        $err = 'Tous les champs sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = 'E-mail invalide.';
    } elseif (strlen($pass) < 8) {
        $err = 'Le mot de passe doit contenir au moins 8 caractères.';
    } else {
        try {
            $q = $pdo->prepare('SELECT id FROM candidat WHERE email = :e LIMIT 1');
            $q->execute(['e' => $email]);

            if ($q->fetch()) {
                $err = 'Cet e-mail est déjà utilisé.';
            } else {
                $q = $pdo->prepare(
                    'INSERT INTO candidat
                    (nom, prenom, email, mot_de_passe, telephone, id_ville)
                    VALUES (:n, :p, :e, :m, :t, :v)'
                );
                $q->execute([
                    'n' => $nom,
                    'p' => $prenom,
                    'e' => $email,
                    'm' => Auth::hashPassword($pass),
                    't' => $tel,
                    'v' => $idVille
                ]);

                $msg = 'Inscription réussie. Vous pouvez maintenant vous connecter.';
            }
        } catch (PDOException $e) {
            $err = 'Erreur lors de l’inscription.';
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription candidat - JobLink Bénin</title>
</head>
<body>
    <h1>Créer un compte candidat</h1>

    <?php if ($msg): ?>
        <p><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <?php if ($err): ?>
        <p><?= htmlspecialchars($err) ?></p>
    <?php endif; ?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
    <form method="post">
        <label>Nom</label><br>
        <input name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required><br><br>

        <label>Prénom</label><br>
        <input name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required><br><br>

        <label>E-mail</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required><br><br>

        <label>Téléphone</label><br>
        <input name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" required><br><br>

        <label>Ville</label><br>
        <select name="id_ville" required>
            <option value="">-- Choisir une ville --</option>
            <?php foreach ($villes as $ville): ?>
                <option value="<?= (int) $ville['id'] ?>" <?= ((int)($_POST['id_ville'] ?? 0) === (int)$ville['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ville['libelle']) ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Mot de passe</label><br>
        <input type="password" name="mot_de_passe" minlength="8" required><br><br>

        <button type="submit">Créer mon compte</button>
    </form>

    <p><a href="login.php">Se connecter</a></p>
</body>
</html>
